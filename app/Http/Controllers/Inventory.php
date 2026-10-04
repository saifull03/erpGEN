<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class Inventory extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = StockMovement::query()->with(['product', 'user']);

        if ($productId = $request->input('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
            });
        }

        $movements = $query->latest()->paginate(25)->withQueryString();
        $valuation = $this->inventoryService->getInventoryValuation();
        $products = Product::query()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'sku', 'current_stock']);

        return view('inventory.index', [
            'movements' => $movements,
            'valuation' => $valuation,
            'products' => $products,
        ]);
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'new_stock' => ['required', 'integer', 'min:0'],
            'type' => ['required', 'string', 'in:adjustment,damage,expired'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);

        $this->inventoryService->adjustStock(
            $product,
            (int) $validated['new_stock'],
            $validated['type'],
            $validated['reason'],
            $validated['notes'],
            $request->user()
        );

        return redirect()->route('inventory.index')->with('success', "Stock for {$product->name} adjusted to {$validated['new_stock']} units.");
    }

    public function alerts()
    {
        $lowStock = $this->inventoryService->getLowStockProducts();
        $outOfStock = $this->inventoryService->getOutOfStockProducts();
        $expiring7Days = $this->inventoryService->getExpiringProducts(7);
        $expiring30Days = $this->inventoryService->getExpiringProducts(30);

        return view('inventory.alerts', [
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,
            'expiring7Days' => $expiring7Days,
            'expiring30Days' => $expiring30Days,
        ]);
    }
}
