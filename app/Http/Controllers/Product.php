<?php

namespace App\Http\Controllers;

use App\Models\Brand as BrandModel;
use App\Models\Category as CategoryModel;
use App\Models\Product as ProductModel;
use App\Models\Unit as UnitModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Product extends Controller
{
    public function index(Request $request)
    {
        $query = ProductModel::query()->with(['category', 'brand', 'unit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        return view('products.index', [
            'products' => $query->latest()->paginate(20),
            'categories' => CategoryModel::all(),
            'brands' => BrandModel::all(),
        ]);
    }

    public function create()
    {
        return view('products.create', [
            'categories' => CategoryModel::all(),
            'brands' => BrandModel::all(),
            'units' => UnitModel::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'current_stock' => ['nullable', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        ProductModel::query()->create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(ProductModel $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => CategoryModel::all(),
            'brands' => BrandModel::all(),
            'units' => UnitModel::all(),
        ]);
    }

    public function update(Request $request, ProductModel $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'current_stock' => ['nullable', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(ProductModel $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
