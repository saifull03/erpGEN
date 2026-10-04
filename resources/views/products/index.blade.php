<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Add Product</h3>
            <form method="POST" action="{{ route('products.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <input name="sku" placeholder="SKU" class="border rounded p-2" required>
                <input name="barcode" placeholder="Barcode" class="border rounded p-2">
                <input name="name" placeholder="Product name" class="border rounded p-2" required>
                <input name="purchase_price" type="number" step="0.01" placeholder="Purchase price" class="border rounded p-2" required>
                <input name="selling_price" type="number" step="0.01" placeholder="Selling price" class="border rounded p-2" required>
                <input name="current_stock" type="number" placeholder="Current stock" class="border rounded p-2" value="0">
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-3">Save</button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">SKU</th>
                        <th class="px-4 py-3 text-left">Barcode</th>
                        <th class="px-4 py-3 text-left">Stock</th>
                        <th class="px-4 py-3 text-left">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($products as $product)
                        <tr>
                            <td class="px-4 py-3">{{ $product->name }}</td>
                            <td class="px-4 py-3">{{ $product->sku }}</td>
                            <td class="px-4 py-3">{{ $product->barcode ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $product->current_stock }}</td>
                            <td class="px-4 py-3">৳ {{ number_format($product->selling_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
