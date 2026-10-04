<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function createPurchase(array $data, array $items, User $user): Purchase
    {
        if (empty($items)) {
            throw ValidationException::withMessages(['items' => 'Add at least one product to the purchase order.']);
        }

        return DB::transaction(function () use ($data, $items, $user) {
            $supplier = Supplier::query()->findOrFail($data['supplier_id']);

            $invoiceNumber = $data['purchase_invoice_number'] ?? null;
            if (empty($invoiceNumber)) {
                $invoiceNumber = 'PUR-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }

            $subtotal = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $product = Product::query()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $price = (float) $item['unit_price'];
                $discount = isset($item['discount']) ? (float) $item['discount'] : 0;
                $tax = isset($item['tax']) ? (float) $item['tax'] : 0;
                $itemSubtotal = round(($qty * $price) - $discount + $tax, 2);

                $subtotal += $itemSubtotal;
                $processedItems[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount' => $discount,
                    'tax' => $tax,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discountAmount = isset($data['discount_amount']) ? (float) $data['discount_amount'] : 0;
            $taxAmount = isset($data['tax_amount']) ? (float) $data['tax_amount'] : 0;
            $total = round(max(0, $subtotal - $discountAmount + $taxAmount), 2);
            $paid = isset($data['paid']) ? min($total, max(0, (float) $data['paid'])) : 0;
            $due = round(max(0, $total - $paid), 2);
            $status = $data['status'] ?? 'received';

            $purchase = Purchase::query()->create([
                'purchase_invoice_number' => $invoiceNumber,
                'supplier_id' => $supplier->id,
                'user_id' => $user->id,
                'date' => $data['date'] ?? now()->toDateString(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'paid' => $paid,
                'due' => $due,
                'status' => $status,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($processedItems as $pItem) {
                $product = $pItem['product'];
                $qty = $pItem['quantity'];

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $pItem['unit_price'],
                    'discount' => $pItem['discount'],
                    'tax' => $pItem['tax'],
                    'subtotal' => $pItem['subtotal'],
                ]);

                // If received, update current inventory and log stock movement
                if ($status === 'received') {
                    $prevStock = $product->current_stock;
                    $newStock = $prevStock + $qty;
                    $product->update([
                        'current_stock' => $newStock,
                        'purchase_price' => $pItem['unit_price'], // update latest purchase price
                    ]);

                    StockMovement::query()->create([
                        'product_id' => $product->id,
                        'type' => 'purchase',
                        'quantity' => $qty,
                        'previous_stock' => $prevStock,
                        'new_stock' => $newStock,
                        'reference' => $purchase->purchase_invoice_number,
                        'user_id' => $user->id,
                        'notes' => "Received from Supplier {$supplier->company_name}",
                    ]);
                }
            }

            // Update Supplier Current Payable Balance
            if ($due > 0) {
                $supplier->increment('current_payable_balance', $due);
            }

            // Ledger Entries
            LedgerService::record(
                'purchase',
                $purchase->id,
                $purchase->purchase_invoice_number,
                "Purchase #{$purchase->purchase_invoice_number} from {$supplier->company_name}",
                $total,
                0,
                $user
            );

            LedgerService::record(
                'supplier',
                $supplier->id,
                $purchase->purchase_invoice_number,
                "Purchase Invoice #{$purchase->purchase_invoice_number}",
                $paid,
                $total,
                $user
            );

            // Audit Log
            AuditService::log('create_purchase', 'Purchases', (string) $purchase->id, null, [
                'invoice' => $purchase->purchase_invoice_number,
                'total' => $total,
                'supplier' => $supplier->company_name,
            ], $user);

            return $purchase;
        });
    }
}
