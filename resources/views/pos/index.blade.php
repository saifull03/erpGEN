<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Point of Sale</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 rounded bg-emerald-100 px-4 py-3 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('pos.store') }}" class="flex gap-3 mb-6">
                    @csrf
                    <input name="barcode" placeholder="Scan barcode or enter SKU" class="border rounded p-2 flex-1" required autofocus>
                    <input name="quantity" type="number" min="1" value="1" class="border rounded p-2 w-24" required>
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
                                <th class="px-3 py-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="px-3 py-2">{{ $product->name }}</td>
                                    <td class="px-3 py-2">
                                        <form method="POST" action="{{ route('pos.update', $product) }}" class="flex items-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <input name="quantity" type="number" min="1" max="{{ $product->current_stock }}" value="{{ $cart[$product->id] }}" class="w-16 rounded border p-1">
                                            <button class="text-xs text-indigo-600" type="submit">Update</button>
                                        </form>
                                    </td>
                                    <td class="px-3 py-2">৳ {{ number_format($product->selling_price, 2) }}</td>
                                    <td class="px-3 py-2">৳ {{ number_format($product->selling_price * $cart[$product->id], 2) }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <form method="POST" action="{{ route('pos.remove', $product) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600" type="submit">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($products->isEmpty())
                                <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">No products in the current sale.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Current Sale</h3>
                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between"><span>Subtotal</span><span>৳ {{ number_format($subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span>Discount</span><span>৳ 0.00</span></div>
                    <div class="flex justify-between"><span>VAT</span><span>৳ 0.00</span></div>
                    <div class="flex justify-between font-semibold text-gray-900"><span>Total</span><span>৳ {{ number_format($subtotal, 2) }}</span></div>
                </div>
                <form method="POST" action="{{ route('pos.complete') }}" class="mt-6 space-y-3">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700" for="member_number">Membership number <span class="font-normal text-gray-500">(optional)</span></label>
                    <input id="member_number" name="member_number" type="text" value="{{ old('member_number') }}" placeholder="Enter member number" class="border rounded p-2 w-full">
                    <input name="paid_amount" type="number" step="0.01" min="0" value="{{ number_format($subtotal, 2, '.', '') }}" class="border rounded p-2 w-full" required>
                    <button type="submit" class="w-full bg-emerald-600 text-white rounded px-4 py-2" @disabled($products->isEmpty())>Complete Sale</button>
                </form>
                <form method="POST" action="{{ route('pos.clear') }}" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-gray-200 text-gray-800 rounded px-4 py-2" @disabled($products->isEmpty())>Clear Cart</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
