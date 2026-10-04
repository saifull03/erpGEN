<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReturnService
{
    protected ShiftService $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function processSaleReturn(Sale $sale, array $itemsToReturn, string $paymentMethod = 'cash', ?string $reason = null, ?User $user = null): SaleReturn
    {
        $user = $user ?? auth()->user();

        if (empty($itemsToReturn)) {
            throw ValidationException::withMessages(['items' => 'Select at least one product item to return.']);
        }

        return DB::transaction(function () use ($sale, $itemsToReturn, $paymentMethod, $reason, $user) {
            $saleItems = $sale->items()->get()->keyBy('id');
            $totalRefund = 0;
            $returnRecords = [];

            foreach ($itemsToReturn as $saleItemId => $returnQty) {
                $returnQty = (int) $returnQty;
                if ($returnQty <= 0) continue;

                $saleItem = $saleItems->get($saleItemId);
                if (! $saleItem) {
                    throw ValidationException::withMessages(['items' => 'Invalid sale item selected.']);
                }

                // Check already returned quantity
                $alreadyReturned = (int) $saleItem->saleReturnItems()->sum('quantity');
                $maxReturnable = $saleItem->quantity - $alreadyReturned;

                if ($returnQty > $maxReturnable) {
                    throw ValidationException::withMessages([
                        'items' => "Cannot return more than purchased quantity ({$maxReturnable}) for item #{$saleItem->id}.",
                    ]);
                }

                $refundSubtotal = round($saleItem->unit_price * $returnQty, 2);
                $totalRefund += $refundSubtotal;

                $returnRecords[] = [
                    'sale_item' => $saleItem,
                    'quantity' => $returnQty,
                    'unit_price' => $saleItem->unit_price,
                    'refund_subtotal' => $refundSubtotal,
                ];
            }

            if (empty($returnRecords)) {
                throw ValidationException::withMessages(['items' => 'No valid items with quantity greater than zero.']);
            }

            $totalRefund = round($totalRefund, 2);
            $returnNumber = 'RET-SALE-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            // Create Return Record
            $saleReturn = SaleReturn::query()->create([
                'return_number' => $returnNumber,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'user_id' => $user->id,
                'total_refund' => $totalRefund,
                'payment_method' => $paymentMethod,
                'reason' => $reason,
                'status' => 'completed',
            ]);

            // Process Items, restore inventory
            foreach ($returnRecords as $rec) {
                $saleItem = $rec['sale_item'];
                $product = $saleItem->product;
                $qty = $rec['quantity'];

                $saleReturn->items()->create([
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $rec['unit_price'],
                    'refund_subtotal' => $rec['refund_subtotal'],
                ]);

                // Restore stock
                $prevStock = $product->current_stock;
                $newStock = $prevStock + $qty;
                $product->update(['current_stock' => $newStock]);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'sale_return',
                    'quantity' => $qty,
                    'previous_stock' => $prevStock,
                    'new_stock' => $newStock,
                    'reference' => $saleReturn->return_number,
                    'user_id' => $user->id,
                    'notes' => "Returned from Sale #{$sale->invoice_number}. Reason: {$reason}",
                ]);
            }

            // Update Shift cash refund if cash refund given
            if ($paymentMethod === 'cash') {
                $activeShift = $this->shiftService->getActiveShift($user);
                if ($activeShift) {
                    $this->shiftService->recordCashRefund($activeShift, $totalRefund);
                }
            }

            // Deduct Points from Member if applicable
            if ($sale->member_id && $sale->member) {
                $member = $sale->member;
                $pointsPerHundred = $member->membershipType ? (int) $member->membershipType->reward_points : 1;
                $pointsToDeduct = (int) floor(($totalRefund / 100) * $pointsPerHundred);

                if ($pointsToDeduct > 0) {
                    $member->decrement('points', min($member->points, $pointsToDeduct));
                    $member->decrement('total_purchase_amount', min($member->total_purchase_amount, $totalRefund));

                    $member->pointLogs()->create([
                        'type' => 'cancelled',
                        'points' => -$pointsToDeduct,
                        'reference' => $saleReturn->return_number,
                        'notes' => "Reversed points due to Sale Return #{$saleReturn->return_number}",
                        'user_id' => $user->id,
                    ]);
                }
            }

            // Ledger
            LedgerService::record(
                'sales',
                $sale->id,
                $saleReturn->return_number,
                "Sales Return #{$saleReturn->return_number} for Invoice #{$sale->invoice_number}",
                0,
                $totalRefund,
                $user
            );

            // Audit
            AuditService::log('sale_return', 'Returns', (string) $saleReturn->id, null, [
                'return_number' => $saleReturn->return_number,
                'total_refund' => $totalRefund,
                'sale_invoice' => $sale->invoice_number,
            ], $user);

            return $saleReturn;
        });
    }

    public function processPurchaseReturn(Purchase $purchase, array $itemsToReturn, ?string $reason = null, ?User $user = null): PurchaseReturn
    {
        $user = $user ?? auth()->user();

        if (empty($itemsToReturn)) {
            throw ValidationException::withMessages(['items' => 'Select at least one purchase item to return.']);
        }

        return DB::transaction(function () use ($purchase, $itemsToReturn, $reason, $user) {
            $purchaseItems = $purchase->items()->get()->keyBy('id');
            $totalAmount = 0;
            $returnRecords = [];

            foreach ($itemsToReturn as $purchaseItemId => $returnQty) {
                $returnQty = (int) $returnQty;
                if ($returnQty <= 0) continue;

                $purchaseItem = $purchaseItems->get($purchaseItemId);
                if (! $purchaseItem) {
                    throw ValidationException::withMessages(['items' => 'Invalid purchase item.']);
                }

                if ($returnQty > $purchaseItem->quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Cannot return more than purchased quantity ({$purchaseItem->quantity}) for item #{$purchaseItem->id}.",
                    ]);
                }

                $subtotal = round($purchaseItem->unit_price * $returnQty, 2);
                $totalAmount += $subtotal;

                $returnRecords[] = [
                    'purchase_item' => $purchaseItem,
                    'quantity' => $returnQty,
                    'unit_price' => $purchaseItem->unit_price,
                    'subtotal' => $subtotal,
                ];
            }

            $totalAmount = round($totalAmount, 2);
            $returnNumber = 'RET-PUR-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $purchaseReturn = PurchaseReturn::query()->create([
                'return_number' => $returnNumber,
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'user_id' => $user->id,
                'date' => now()->toDateString(),
                'total_amount' => $totalAmount,
                'reason' => $reason,
                'status' => 'completed',
            ]);

            foreach ($returnRecords as $rec) {
                $purchaseItem = $rec['purchase_item'];
                $product = $purchaseItem->product;
                $qty = $rec['quantity'];

                $purchaseReturn->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $rec['unit_price'],
                    'subtotal' => $rec['subtotal'],
                ]);

                // Reduce inventory
                $prevStock = $product->current_stock;
                $newStock = max(0, $prevStock - $qty);
                $product->update(['current_stock' => $newStock]);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'purchase_return',
                    'quantity' => -$qty,
                    'previous_stock' => $prevStock,
                    'new_stock' => $newStock,
                    'reference' => $purchaseReturn->return_number,
                    'user_id' => $user->id,
                    'notes' => "Returned to Supplier on Purchase #{$purchase->purchase_invoice_number}",
                ]);
            }

            // Adjust supplier balance
            if ($purchase->supplier) {
                $purchase->supplier->decrement('current_payable_balance', min($purchase->supplier->current_payable_balance, $totalAmount));
            }

            // Ledger
            LedgerService::record(
                'supplier',
                $purchase->supplier_id,
                $purchaseReturn->return_number,
                "Purchase Return #{$purchaseReturn->return_number} for invoice #{$purchase->purchase_invoice_number}",
                $totalAmount,
                0,
                $user
            );

            // Audit
            AuditService::log('purchase_return', 'Purchases', (string) $purchaseReturn->id, null, [
                'return_number' => $purchaseReturn->return_number,
                'total_amount' => $totalAmount,
            ], $user);

            return $purchaseReturn;
        });
    }
}
