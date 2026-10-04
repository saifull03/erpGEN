<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">SKU: <span class="font-mono font-bold">{{ $product->sku }}</span> | Barcode: <span class="font-mono font-bold text-indigo-600">{{ $product->barcode }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.barcode', $product) }}" target="_blank" class="px-3 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold rounded-lg transition">
                    Print Barcodes
                </a>
                <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                    Edit Product
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold uppercase text-gray-400">Current Stock</span>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $product->current_stock }} <span class="text-sm font-normal text-gray-500">{{ $product->unit?->short_name }}</span></p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold uppercase text-gray-400">Selling Price</span>
                <p class="text-2xl font-black text-indigo-700 mt-1">৳ {{ number_format($product->selling_price, 2) }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold uppercase text-gray-400">Purchase Price</span>
                <p class="text-2xl font-black text-gray-700 mt-1">৳ {{ number_format($product->purchase_price, 2) }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold uppercase text-gray-400">Profit Margin</span>
                @php $margin = $product->purchase_price > 0 ? (($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100 : 0; @endphp
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($margin, 1) }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Price History Log -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-800 text-sm">
                    Price Change History
                </div>
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Date</th>
                            <th class="px-4 py-2.5 text-right">Old Price</th>
                            <th class="px-4 py-2.5 text-right">New Price</th>
                            <th class="px-4 py-2.5 text-left">Reason / Staff</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($product->priceHistories as $ph)
                            <tr>
                                <td class="px-4 py-2.5 text-gray-500">{{ $ph->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2.5 text-right text-gray-500">৳ {{ number_format($ph->old_selling_price, 2) }}</td>
                                <td class="px-4 py-2.5 text-right font-bold text-indigo-600">৳ {{ number_format($ph->new_selling_price, 2) }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="font-medium text-gray-800">{{ $ph->reason ?? 'Price update' }}</div>
                                    <div class="text-[10px] text-gray-400">By: {{ $ph->user?->name ?? 'Admin' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No price changes recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Recent Stock Movements -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-800 text-sm">
                    Recent Stock Movements
                </div>
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Date</th>
                            <th class="px-4 py-2.5 text-left">Type</th>
                            <th class="px-4 py-2.5 text-center">Qty</th>
                            <th class="px-4 py-2.5 text-right">New Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($product->stockMovements as $sm)
                            <tr>
                                <td class="px-4 py-2.5 text-gray-500">{{ $sm->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-2.5 font-semibold text-gray-700 uppercase text-[10px]">{{ $sm->type }}</td>
                                <td class="px-4 py-2.5 text-center font-bold {{ $sm->quantity > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $sm->quantity > 0 ? '+' : '' }}{{ $sm->quantity }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-bold text-gray-900">{{ $sm->new_stock }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No stock movements recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
