<?php

namespace App\Services;

use App\Models\HeldSale;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleService
{
    protected ShiftService $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function createSale(array $data, array $cart, User $cashier): Sale
    {
        if (empty($cart)) {
            throw ValidationException::withMessages(['cart' => 'The cart is empty.']);
        }

        return DB::transaction(function () use ($data, $cart, $cashier) {
            $productIds = array_keys($cart);
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 1. Resolve Member (if provided)
            $member = null;
            $memberNumber = $data['member_number'] ?? null;
            if (! empty($memberNumber)) {
                $member = Member::query()
                    ->with('membershipType')
                    ->where('member_number', $memberNumber)
                    ->where('status', 'active')
                    ->first();

                if (! $member) {
                    throw ValidationException::withMessages([
                        'member_number' => "Active member not found for card #{$memberNumber}.",
                    ]);
                }
            }

            // 2. Calculate Subtotal & Line Items
            $subtotal = 0;
            $lineItems = [];

            foreach ($cart as $productId => $qty) {
                $product = $products->get($productId);
                $quantity = (int) $qty;

                if (! $product || $product->status !== 'active') {
                    throw ValidationException::withMessages(['cart' => "Product is inactive or unavailable."]);
                }

                if ($product->current_stock < $quantity) {
                    throw ValidationException::withMessages(['cart' => "Insufficient stock for {$product->name}. Current stock: {$product->current_stock}"]);
                }

                $unitPrice = (float) $product->selling_price;
                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => 0,
                    'tax' => 0,
                    'subtotal' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);

            // 3. Calculate Discounts & Tax
            $membershipDiscount = 0;
            if ($member && $member->membershipType && $member->membershipType->discount_percentage > 0) {
                $membershipDiscount = round(($subtotal * (float) $member->membershipType->discount_percentage) / 100, 2);
            }

            $invoiceDiscount = isset($data['invoice_discount']) ? max(0, (float) $data['invoice_discount']) : 0;
            $totalDiscount = min($subtotal, round($membershipDiscount + $invoiceDiscount, 2));

            $taxRate = isset($data['tax_rate']) ? (float) $data['tax_rate'] : 0; // standard 0 or percentage
            $taxAmount = round((($subtotal - $totalDiscount) * $taxRate) / 100, 2);

            $grandTotal = round(max(0, $subtotal - $totalDiscount + $taxAmount), 2);

            // 4. Resolve Payment Breakdown
            $paymentsInput = $data['payments'] ?? [];
            if (is_string($paymentsInput) && !empty($paymentsInput)) {
                $paymentsInput = json_decode($paymentsInput, true) ?? [];
            }
            $totalPaid = 0;

            if (empty($paymentsInput)) {
                $paidInput = isset($data['paid_amount']) ? (float) $data['paid_amount'] : $grandTotal;
                $paymentsInput = [
                    ['method' => $data['payment_method'] ?? 'cash', 'amount' => $paidInput, 'reference' => null]
                ];
            }

            foreach ($paymentsInput as $p) {
                $totalPaid += max(0, (float) ($p['amount'] ?? 0));
            }
            $totalPaid = round($totalPaid, 2);

            $changeAmount = max(0, round($totalPaid - $grandTotal, 2));
            $effectivePaid = min($grandTotal, $totalPaid);
            $dueAmount = max(0, round($grandTotal - $effectivePaid, 2));

            // Determine primary payment method name
            $primaryMethod = count($paymentsInput) > 1 ? 'split' : ($paymentsInput[0]['method'] ?? 'cash');

            // Active Shift
            $activeShift = $this->shiftService->getActiveShift($cashier);

            // 5. Generate Invoice & Create Sale Record
            $invoiceNumber = 'INV-' . date('YmdHis') . '-' . strtoupper(Str::random(4));

            $sale = Sale::query()->create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $cashier->id,
                'customer_id' => $member ? $member->customer_id : ($data['customer_id'] ?? null),
                'member_id' => $member?->id,
                'shift_id' => $activeShift?->id,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'invoice_discount' => $invoiceDiscount,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'paid_amount' => $effectivePaid,
                'due_amount' => $dueAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $primaryMethod,
                'status' => $dueAmount == 0 ? 'completed' : 'partial',
                'notes' => $data['notes'] ?? null,
            ]);

            // 6. Create Sale Items, Deduct Stock, and Record Stock Movements
            foreach ($lineItems as $item) {
                $product = $item['product'];
                $qty = $item['quantity'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $item['unit_price'],
                    'discount' => 0,
                    'tax' => 0,
                    'subtotal' => $item['subtotal'],
                ]);

                $prevStock = $product->current_stock;
                $newStock = $prevStock - $qty;

                $product->update(['current_stock' => $newStock]);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => -$qty,
                    'previous_stock' => $prevStock,
                    'new_stock' => $newStock,
                    'reference' => $sale->invoice_number,
                    'user_id' => $cashier->id,
                    'notes' => "Sold at POS invoice #{$sale->invoice_number}",
                ]);
            }

            // 7. Store Payment Transactions & Update Cash Register Shift
            $cashPaidInTransaction = 0;
            foreach ($paymentsInput as $p) {
                $amount = round((float) ($p['amount'] ?? 0), 2);
                if ($amount <= 0) continue;

                $method = $p['method'] ?? 'cash';
                $ref = $p['reference'] ?? null;

                $sale->payments()->create([
                    'amount' => $amount,
                    'payment_method' => $method,
                    'transaction_reference' => $ref,
                    'user_id' => $cashier->id,
                    'payment_date' => now()->toDateString(),
                ]);

                if ($method === 'cash') {
                    $cashPaidInTransaction += max(0, $amount - $changeAmount);
                }
            }

            if ($activeShift && $cashPaidInTransaction > 0) {
                $this->shiftService->recordCashSales($activeShift, $cashPaidInTransaction);
            }

            // 8. Reward Loyalty Points to Member
            if ($member && $grandTotal > 0) {
                $pointsPerHundred = $member->membershipType ? (int) $member->membershipType->reward_points : 1;
                $earnedPoints = (int) floor(($grandTotal / 100) * $pointsPerHundred);

                if ($earnedPoints > 0) {
                    $member->increment('points', $earnedPoints);
                    $member->increment('total_purchase_amount', $grandTotal);
                    $member->increment('total_transactions', 1);

                    $member->pointLogs()->create([
                        'type' => 'earned',
                        'points' => $earnedPoints,
                        'reference' => $sale->invoice_number,
                        'notes' => "Earned {$earnedPoints} points on invoice #{$sale->invoice_number}",
                        'user_id' => $cashier->id,
                    ]);
                }
            }

            // 9. Record Ledgers
            LedgerService::record(
                'sales',
                $sale->id,
                $sale->invoice_number,
                "POS Sale #{$sale->invoice_number}",
                $grandTotal,
                0,
                $cashier
            );

            if ($sale->customer_id) {
                LedgerService::record(
                    'customer',
                    $sale->customer_id,
                    $sale->invoice_number,
                    "Customer purchase on #{$sale->invoice_number}",
                    $grandTotal,
                    $effectivePaid,
                    $cashier
                );
            }

            // 10. Audit Log
            AuditService::log('create_sale', 'Sales', (string) $sale->id, null, [
                'invoice' => $sale->invoice_number,
                'grand_total' => $grandTotal,
                'paid' => $effectivePaid,
                'items_count' => count($lineItems),
            ], $cashier);

            return $sale;
        });
    }

    public function holdSale(array $cart, ?int $customerId, ?int $memberId, ?string $memberNumber, User $cashier, ?string $notes = null): HeldSale
    {
        if (empty($cart)) {
            throw ValidationException::withMessages(['cart' => 'Cannot hold an empty cart.']);
        }

        $holdCode = 'HOLD-' . date('His') . '-' . strtoupper(Str::random(3));

        $subtotal = 0;
        $products = Product::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');
        foreach ($cart as $id => $qty) {
            if ($p = $products->get($id)) {
                $subtotal += $p->selling_price * (int) $qty;
            }
        }

        return HeldSale::query()->create([
            'reference_code' => $holdCode,
            'cashier_id' => $cashier->id,
            'customer_id' => $customerId,
            'member_id' => $memberId,
            'member_number' => $memberNumber,
            'cart_data' => $cart,
            'subtotal' => round($subtotal, 2),
            'notes' => $notes,
        ]);
    }

    public function getHeldSales(User $cashier)
    {
        return HeldSale::query()
            ->where('cashier_id', $cashier->id)
            ->latest()
            ->get();
    }

    public function resumeHeldSale(string $referenceCode): ?HeldSale
    {
        $held = HeldSale::query()->where('reference_code', $referenceCode)->first();
        if ($held) {
            $held->delete();
        }
        return $held;
    }

    public function deleteHeldSale(string $referenceCode): bool
    {
        return (bool) HeldSale::query()->where('reference_code', $referenceCode)->delete();
    }
}
