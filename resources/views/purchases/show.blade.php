<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Purchase Order #{{ $purchase->purchase_invoice_number }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Date: {{ $purchase->date->format('F d, Y') }} | Status: <span class="font-bold uppercase text-indigo-600">{{ $purchase->status }}</span></p>
            </div>
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">
                Back to Purchases
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ returnModalOpen: false }">
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm text-emerald-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Items Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800">Purchased Products</h3>
                        <button type="button" @click="returnModalOpen = true" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-bold rounded-lg transition">
                            Return to Supplier
                        </button>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="px-5 py-3 text-left">Product</th>
                                <th class="px-5 py-3 text-center">Qty</th>
                                <th class="px-5 py-3 text-right">Unit Cost</th>
                                <th class="px-5 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($purchase->items as $item)
                                <tr>
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-gray-900">{{ $item->product?->name }}</div>
                                        <div class="text-xs text-gray-400 font-mono">{{ $item->product?->sku }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center font-bold text-gray-800">{{ $item->quantity }}</td>
                                    <td class="px-5 py-3.5 text-right font-medium text-gray-600">৳ {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-5 py-3.5 text-right font-bold text-gray-900">৳ {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Purchase Returns History -->
                @if ($purchase->returns->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
                        <div class="p-4 bg-red-50/60 border-b border-red-100 font-bold text-red-800 text-sm">
                            Supplier Return History
                        </div>
                        <div class="p-4 space-y-3">
                            @foreach ($purchase->returns as $ret)
                                <div class="p-3 bg-red-50/30 rounded-lg border border-red-100 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-mono font-bold text-red-700">{{ $ret->return_number }}</span>
                                        <span class="text-gray-500 ml-2">Reason: {{ $ret->reason }}</span>
                                    </div>
                                    <div class="font-bold text-red-700 text-sm">
                                        Total: ৳ {{ number_format($ret->total_amount, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Summary & Supplier Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3 text-sm">
                    <h3 class="font-bold text-gray-900 uppercase tracking-wider pb-2 border-b border-gray-100 text-xs">Order Summary</h3>
                    
                    <div class="flex justify-between text-gray-600"><span>Subtotal:</span><span class="font-semibold text-gray-900">৳ {{ number_format($purchase->subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-emerald-600"><span>Discount:</span><span>- ৳ {{ number_format($purchase->discount_amount, 2) }}</span></div>
                    <div class="flex justify-between text-gray-600"><span>Tax / Freight:</span><span>৳ {{ number_format($purchase->tax_amount, 2) }}</span></div>
                    <div class="flex justify-between text-base font-black text-gray-900 pt-2 border-t border-gray-200">
                        <span>Total:</span>
                        <span class="text-indigo-700">৳ {{ number_format($purchase->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-700 font-bold"><span>Paid:</span><span>৳ {{ number_format($purchase->paid, 2) }}</span></div>
                    <div class="flex justify-between {{ $purchase->due > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                        <span>Due Balance:</span>
                        <span>৳ {{ number_format($purchase->due, 2) }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3 text-xs">
                    <h3 class="font-bold text-gray-900 uppercase tracking-wider pb-2 border-b border-gray-100">Supplier Details</h3>
                    <div>
                        <span class="text-gray-400 uppercase">Company:</span>
                        <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $purchase->supplier?->company_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase">Contact Person:</span>
                        <p class="text-gray-700 font-medium">{{ $purchase->supplier?->contact_person }} ({{ $purchase->supplier?->phone }})</p>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase">Current Payable Balance:</span>
                        <p class="font-bold text-red-600 text-sm mt-0.5">৳ {{ number_format($purchase->supplier?->current_payable_balance, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Return Modal -->
        <div x-show="returnModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="returnModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-60"></div>
                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                        <h3 class="text-base font-bold text-gray-900">Return Items to Supplier</h3>
                        <button @click="returnModalOpen = false" class="text-gray-400 font-bold">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('purchases.return', $purchase) }}">
                        @csrf
                        <div class="space-y-3 max-h-60 overflow-y-auto mb-4 border border-gray-200 rounded-lg p-3">
                            @foreach ($purchase->items as $item)
                                <div class="flex items-center justify-between text-xs py-2 border-b border-gray-100 last:border-0">
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-800">{{ $item->product?->name }}</div>
                                        <div class="text-gray-400">Purchased: {{ $item->quantity }} • ৳ {{ number_format($item->unit_price, 2) }} each</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-gray-500">Return Qty:</label>
                                        <input type="number" name="items[{{ $item->id }}]" min="0" max="{{ $item->quantity }}" value="0" class="w-16 text-center text-xs border-gray-300 rounded-md font-bold">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Reason for Return</label>
                            <input name="reason" placeholder="e.g. Damaged packaging, short expiry, excess order" class="w-full text-xs rounded-lg border-gray-300" required>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="returnModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg">Process Supplier Return</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
