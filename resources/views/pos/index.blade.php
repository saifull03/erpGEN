<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Point of Sale</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('pos.store') }}" class="flex gap-3 mb-6">
                    @csrf
                    <input name="barcode" placeholder="Scan barcode or search SKU" class="border rounded p-2 flex-1" required>
                    <input name="quantity" type="number" value="1" class="border rounded p-2 w-24">
                    <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2">Add</button>
                </form>

                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Product</th>
                                <th class="px-3 py-2 text-left">Qty</th>
                                <th class="px-3 py-2 text-left">Price</th>
                                <th class="px-3 py-2 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="px-3 py-2">{{ $product->name }}</td>
                                    <td class="px-3 py-2">1</td>
                                    <td class="px-3 py-2">৳ {{ number_format($product->selling_price, 2) }}</td>
                                    <td class="px-3 py-2">৳ {{ number_format($product->selling_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Current Sale</h3>
                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between"><span>Subtotal</span><span>৳ 0.00</span></div>
                    <div class="flex justify-between"><span>Discount</span><span>৳ 0.00</span></div>
                    <div class="flex justify-between"><span>VAT</span><span>৳ 0.00</span></div>
                    <div class="flex justify-between font-semibold text-gray-900"><span>Total</span><span>৳ 0.00</span></div>
                </div>
                <div class="mt-6 space-y-3">
                    <button class="w-full bg-emerald-600 text-white rounded px-4 py-2">Complete Sale</button>
                    <button class="w-full bg-gray-200 text-gray-800 rounded px-4 py-2">Hold Sale</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
