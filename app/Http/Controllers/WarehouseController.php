<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseProductStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(Request $request): View
    {
        $selectedWarehouseId = $request->query('warehouse_id');
        $warehouses = Warehouse::with('branch')->withCount('productStocks')->get();

        $activeWarehouse = $selectedWarehouseId ? Warehouse::with('branch')->find($selectedWarehouseId) : $warehouses->first();

        $stocks = collect();
        if ($activeWarehouse) {
            $search = $request->query('search');
            $stocks = WarehouseProductStock::with(['product.category', 'product.brand', 'product.unit'])
                ->where('warehouse_id', $activeWarehouse->id)
                ->when($search, function ($query, $search) {
                    $query->whereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%")
                          ->orWhere('barcode', 'like', "%{$search}%");
                    });
                })
                ->paginate(20)
                ->withQueryString();
        }

        $branches = Branch::where('status', 'active')->get();

        return view('warehouses.index', compact('warehouses', 'activeWarehouse', 'stocks', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['is_primary'] = $request->boolean('is_primary');

        if ($validated['is_primary']) {
            Warehouse::where('branch_id', $validated['branch_id'])->update(['is_primary' => false]);
        }

        $warehouse = Warehouse::create($validated);

        return redirect()->route('warehouses.index', ['warehouse_id' => $warehouse->id])->with('success', 'Warehouse created successfully.');
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['is_primary'] = $request->boolean('is_primary');

        if ($validated['is_primary']) {
            Warehouse::where('branch_id', $validated['branch_id'])->where('id', '!=', $warehouse->id)->update(['is_primary' => false]);
        }

        $warehouse->update($validated);

        return redirect()->route('warehouses.index', ['warehouse_id' => $warehouse->id])->with('success', 'Warehouse updated successfully.');
    }
}
