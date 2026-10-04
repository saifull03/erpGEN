<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Product Catalog</h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage supermarket products, prices, barcodes, stock levels, and categories.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory.alerts') }}" class="inline-flex items-center px-3 py-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold rounded-lg hover:bg-amber-100 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    Stock Alerts
                </a>
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Add Product
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm text-emerald-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Search</label>
                        <input name="search" value="{{ request('search') }}" placeholder="Name, SKU, Barcode..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Category</label>
                        <select name="category_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Brand</label>
                        <select name="brand_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                            <option value="">All Brands</option>
                            @foreach ($brands as $b)
                                <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Stock Level</label>
                        <select name="stock_status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                            <option value="">All Stock</option>
                            <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low Stock Only</option>
                            <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Out of Stock Only</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-semibold transition">
                            Filter
                        </button>
                        @if (request()->hasAny(['search', 'category_id', 'brand_id', 'stock_status', 'expiring']))
                            <a href="{{ route('products.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Products Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Product / SKU</th>
                            <th class="px-5 py-3.5 text-left">Category & Brand</th>
                            <th class="px-5 py-3.5 text-right">Purchase Price</th>
                            <th class="px-5 py-3.5 text-right">Selling Price</th>
                            <th class="px-5 py-3.5 text-center">Stock</th>
                            <th class="px-5 py-3.5 text-left">Expiry</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900">
                                        <a href="{{ route('products.show', $product) }}" class="hover:underline">
                                            {{ $product->name }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-400 font-mono">
                                        SKU: <span class="text-gray-600 font-semibold">{{ $product->sku }}</span>
                                        @if ($product->barcode)
                                            • Barcode: <span class="text-indigo-600 font-semibold">{{ $product->barcode }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <div class="font-semibold text-gray-800">{{ $product->category?->name ?? 'Uncategorized' }}</div>
                                    <div class="text-gray-400">{{ $product->brand?->name ?? 'No Brand' }}</div>
                                </td>
                                <td class="px-5 py-4 text-right text-gray-600">
                                    ৳ {{ number_format($product->purchase_price, 2) }}
                                </td>
                                <td class="px-5 py-4 text-right font-black text-indigo-700">
                                    ৳ {{ number_format($product->selling_price, 2) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if ($product->current_stock <= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                            0 (Out of Stock)
                                        </span>
                                    @elseif ($product->current_stock <= $product->minimum_stock)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            {{ $product->current_stock }} (Low)
                                        </span>
                                    @else
                                        <span class="font-bold text-emerald-700">{{ $product->current_stock }}</span>
                                        <span class="text-xs text-gray-400">{{ $product->unit?->short_name ?? 'pcs' }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    @if ($product->expiry_date)
                                        <span class="{{ $product->isExpired() ? 'text-red-600 font-bold' : ($product->expiry_date->diffInDays(now()) <= 30 ? 'text-amber-600 font-semibold' : 'text-gray-600') }}">
                                            {{ $product->expiry_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $product->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('products.barcode', $product) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded" title="Print Barcode">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded" title="Edit Product">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete product {{ $product->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded" title="Delete Product">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    No products found in the catalog.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
