<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Add New Product</h2>
                <p class="text-sm text-gray-500 mt-0.5">Register a product with pricing, barcode, inventory levels, and units.</p>
            </div>
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">
                Back to Catalog
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-red-800 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('products.store') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-6">
            @csrf

            <!-- Section 1: Basic Information -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider pb-2 border-b border-gray-100 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Product Name *</label>
                        <input name="name" value="{{ old('name') }}" placeholder="e.g. Fresh Miniket Rice 5kg" class="w-full rounded-lg border-gray-300 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">SKU (Auto or Custom)</label>
                        <input name="sku" value="{{ old('sku') }}" placeholder="Leave blank for auto PRD-XXXX" class="w-full rounded-lg border-gray-300 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Barcode</label>
                        <input name="barcode" value="{{ old('barcode') }}" placeholder="Scan or enter barcode" class="w-full rounded-lg border-gray-300 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Category</label>
                        <select name="category_id" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Brand</label>
                        <select name="brand_id" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">-- Select Brand --</option>
                            @foreach ($brands as $b)
                                <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Pricing & Stock -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider pb-2 border-b border-gray-100 mb-4">Pricing & Inventory</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Purchase Price (৳) *</label>
                        <input name="purchase_price" type="number" step="0.01" min="0" value="{{ old('purchase_price') }}" placeholder="0.00" class="w-full rounded-lg border-gray-300 text-sm font-bold" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Retail Selling Price (৳) *</label>
                        <input name="selling_price" type="number" step="0.01" min="0" value="{{ old('selling_price') }}" placeholder="0.00" class="w-full rounded-lg border-gray-300 text-sm font-bold text-indigo-700" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Wholesale Price (৳)</label>
                        <input name="wholesale_price" type="number" step="0.01" min="0" value="{{ old('wholesale_price') }}" placeholder="0.00" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Measurement Unit</label>
                        <select name="unit_id" class="w-full rounded-lg border-gray-300 text-sm">
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->short_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Opening Stock</label>
                        <input name="current_stock" type="number" min="0" value="{{ old('current_stock', 0) }}" class="w-full rounded-lg border-gray-300 text-sm font-bold text-emerald-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Low Stock Alert Level</label>
                        <input name="minimum_stock" type="number" min="1" value="{{ old('minimum_stock', 5) }}" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Expiry Date</label>
                        <input name="expiry_date" type="date" value="{{ old('expiry_date') }}" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description (Optional)</label>
                <textarea name="description" rows="2" placeholder="Product details, ingredients, shelf location..." class="w-full rounded-lg border-gray-300 text-sm">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                    Save Product
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
