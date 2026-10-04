<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product as ProductModel;
use App\Models\ProductPriceHistory;
use App\Models\Unit;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Product extends Controller
{
    public function index(Request $request)
    {
        $query = ProductModel::query()->with(['category', 'brand', 'unit']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($catId = $request->input('category_id')) {
            $query->where('category_id', $catId);
        }

        if ($brandId = $request->input('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        if ($filter = $request->input('stock_status')) {
            if ($filter === 'low') {
                $query->whereColumn('current_stock', '<=', 'minimum_stock')->where('current_stock', '>', 0);
            } elseif ($filter === 'out') {
                $query->where('current_stock', '<=', 0);
            }
        }

        if ($request->boolean('expiring')) {
            $query->whereNotNull('expiry_date')->where('expiry_date', '<=', now()->addDays(30)->toDateString());
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::query()->where('status', 'active')->get();
        $brands = Brand::query()->where('status', 'active')->get();
        $units = Unit::query()->where('status', 'active')->get();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
        ]);
    }

    public function create()
    {
        return view('products.create', [
            'categories' => Category::query()->where('status', 'active')->get(),
            'brands' => Brand::query()->where('status', 'active')->get(),
            'units' => Unit::query()->where('status', 'active')->get(),
        ]);
    }

    public function store(Request $request)
    {
        if (empty($request->input('sku'))) {
            do {
                $sku = 'PRD-' . strtoupper(Str::random(6));
            } while (ProductModel::query()->where('sku', $sku)->exists());
            $request->merge(['sku' => $sku]);
        }

        if (empty($request->input('barcode'))) {
            $request->merge(['barcode' => $request->input('sku')]);
        }

        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
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
            'batch_number' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . strtolower(Str::random(4));
        $validated['minimum_stock'] = $validated['minimum_stock'] ?? 5;
        $validated['current_stock'] = $validated['current_stock'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'active';

        $product = ProductModel::query()->create($validated);

        AuditService::log('create_product', 'Products', (string) $product->id, null, $product->toArray());

        return redirect()->route('products.index')->with('success', "Product '{$product->name}' created successfully.");
    }

    public function show(ProductModel $product)
    {
        $product->load(['category', 'brand', 'unit', 'priceHistories.user', 'stockMovements' => fn ($q) => $q->latest()->limit(20)]);

        return view('products.show', [
            'product' => $product,
        ]);
    }

    public function edit(ProductModel $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::query()->where('status', 'active')->get(),
            'brands' => Brand::query()->where('status', 'active')->get(),
            'units' => Unit::query()->where('status', 'active')->get(),
        ]);
    }

    public function update(Request $request, ProductModel $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode,' . $product->id],
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
            'expiry_date' => ['nullable', 'date'],
            'batch_number' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ]);

        // Check if price changed -> log price history
        $oldPurchase = (float) $product->purchase_price;
        $newPurchase = (float) $validated['purchase_price'];
        $oldSelling = (float) $product->selling_price;
        $newSelling = (float) $validated['selling_price'];

        if ($oldPurchase != $newPurchase || $oldSelling != $newSelling) {
            ProductPriceHistory::query()->create([
                'product_id' => $product->id,
                'old_purchase_price' => $oldPurchase,
                'new_purchase_price' => $newPurchase,
                'old_selling_price' => $oldSelling,
                'new_selling_price' => $newSelling,
                'user_id' => auth()->id(),
                'reason' => $request->input('price_change_reason', 'Price update from edit form'),
            ]);
        }

        $oldData = $product->toArray();
        $product->update($validated);

        AuditService::log('update_product', 'Products', (string) $product->id, $oldData, $product->toArray());

        return redirect()->route('products.index')->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function destroy(ProductModel $product)
    {
        $name = $product->name;
        $product->delete();

        AuditService::log('delete_product', 'Products', (string) $product->id, ['name' => $name], null);

        return redirect()->route('products.index')->with('success', "Product '{$name}' deleted successfully.");
    }

    public function barcode(ProductModel $product)
    {
        return view('products.barcode', [
            'product' => $product,
        ]);
    }
}
