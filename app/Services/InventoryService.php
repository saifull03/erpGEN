<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    public function adjustStock(Product $product, int $newStock, string $type, string $reason, ?string $notes = null, ?User $user = null): StockMovement
    {
        $user = $user ?? auth()->user();

        return DB::transaction(function () use ($product, $newStock, $type, $reason, $notes, $user) {
            $prevStock = $product->current_stock;
            $diff = $newStock - $prevStock;

            $adjustmentNumber = 'ADJ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $adjustment = StockAdjustment::query()->create([
                'adjustment_number' => $adjustmentNumber,
                'user_id' => $user->id,
                'date' => now()->toDateString(),
                'reason' => $reason,
                'notes' => $notes,
            ]);

            $adjustment->items()->create([
                'product_id' => $product->id,
                'type' => $diff >= 0 ? 'addition' : 'subtraction',
                'quantity' => abs($diff),
                'previous_stock' => $prevStock,
                'new_stock' => $newStock,
            ]);

            $product->update(['current_stock' => $newStock]);

            $movement = StockMovement::query()->create([
                'product_id' => $product->id,
                'type' => $type, // adjustment, damage, expired, general
                'quantity' => $diff,
                'previous_stock' => $prevStock,
                'new_stock' => $newStock,
                'reference' => $adjustment->adjustment_number,
                'user_id' => $user->id,
                'notes' => "Adjustment: {$reason}. " . ($notes ?? ''),
            ]);

            AuditService::log('stock_adjustment', 'Inventory', (string) $product->id, ['stock' => $prevStock], ['stock' => $newStock, 'diff' => $diff, 'reason' => $reason], $user);

            return $movement;
        });
    }

    public function getLowStockProducts(): Collection
    {
        return Product::query()
            ->where('status', 'active')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->with(['category', 'brand', 'unit'])
            ->get();
    }

    public function getOutOfStockProducts(): Collection
    {
        return Product::query()
            ->where('status', 'active')
            ->where('current_stock', '<=', 0)
            ->with(['category', 'brand', 'unit'])
            ->get();
    }

    public function getExpiringProducts(int $days = 30): Collection
    {
        $targetDate = now()->addDays($days)->toDateString();

        return Product::query()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $targetDate)
            ->with(['category', 'brand'])
            ->orderBy('expiry_date')
            ->get();
    }

    public function getInventoryValuation(): array
    {
        $products = Product::query()->where('status', 'active')->get();

        $totalCost = $products->sum(fn ($p) => $p->current_stock * $p->purchase_price);
        $totalRetail = $products->sum(fn ($p) => $p->current_stock * $p->selling_price);
        $totalItems = $products->sum('current_stock');

        return [
            'total_cost_value' => round($totalCost, 2),
            'total_retail_value' => round($totalRetail, 2),
            'estimated_profit' => round($totalRetail - $totalCost, 2),
            'total_units_in_stock' => (int) $totalItems,
            'total_unique_skus' => $products->count(),
        ];
    }
}
