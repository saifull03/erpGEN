<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Sale #{{ $sale->invoice_number }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $sale->created_at->format('F d, Y • h:i:s A') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('sales.thermal', $sale) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Thermal Receipt (80mm)
                </a>
                <a href="{{ route('sales.invoice', $sale) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    A4 Invoice
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ returnModalOpen: false }">
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm text-emerald-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Item breakdown -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800">Purchased Items</h3>
                        <button type="button" @click="returnModalOpen = true" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-bold rounded-lg transition">
                            Process Item Return
                        </button>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="px-5 py-3 text-left">Product</th>
                                <th class="px-5 py-3 text-center">Qty</th>
                                <th class="px-5 py-3 text-right">Unit Price</th>
                                <th class="px-5 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-gray-900">{{ $item->product?->name ?? 'Product' }}</div>
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

                <!-- Sales Returns History (if any) -->
                @if ($sale->returns->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
                        <div class="p-4 bg-red-50/60 border-b border-red-100 font-bold text-red-800 text-sm">
                            Return History
                        </div>
                        <div class="p-4 space-y-3">
                            @foreach ($sale->returns as $ret)
                                <div class="p-3 bg-red-50/30 rounded-lg border border-red-100 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-mono font-bold text-red-700">{{ $ret->return_number }}</span>
                                        <span class="text-gray-500 ml-2">Reason: {{ $ret->reason ?? 'Customer return' }}</span>
                                    </div>
                                    <div class="font-bold text-red-700 text-sm">
                                        Refunded: ৳ {{ number_format($ret->total_refund, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right 1 Col: Summary, Payments, & Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                    <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider pb-2 border-b border-gray-100">Financial Summary</h3>
                    
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between"><span>Subtotal</span><span class="font-semibold text-gray-900">৳ {{ number_format($sale->subtotal, 2) }}</span></div>
                        <div class="flex justify-between text-emerald-600"><span>Discount</span><span>- ৳ {{ number_format($sale->discount_amount, 2) }}</span></div>
                        <div class="flex justify-between"><span>VAT / Tax</span><span>৳ {{ number_format($sale->tax_amount, 2) }}</span></div>
                        <div class="flex justify-between text-base font-black text-gray-900 pt-2 border-t border-gray-200">
                            <span>Grand Total</span>
                            <span class="text-indigo-700">৳ {{ number_format($sale->grand_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-700 font-bold"><span>Paid</span><span>৳ {{ number_format($sale->paid_amount, 2) }}</span></div>
                        @if ($sale->due_amount > 0)
                            <div class="flex justify-between text-red-600 font-bold"><span>Due Amount</span><span>৳ {{ number_format($sale->due_amount, 2) }}</span></div>
                        @endif
                        @if ($sale->change_amount > 0)
                            <div class="flex justify-between text-gray-500"><span>Change Given</span><span>৳ {{ number_format($sale->change_amount, 2) }}</span></div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3 text-xs">
                    <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider pb-2 border-b border-gray-100">Customer & Staff</h3>
                    
                    <div>
                        <span class="text-gray-400 uppercase">Customer:</span>
                        <p class="font-bold text-gray-800 mt-0.5">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</p>
                    </div>

                    @if ($sale->member)
                        <div>
                            <span class="text-gray-400 uppercase">Member Details:</span>
                            <p class="font-bold text-indigo-700 mt-0.5">{{ $sale->member->name }} (Card: {{ $sale->member->member_number }})</p>
                            <p class="text-gray-500">Tier: {{ $sale->member->membershipType?->name }} ({{ $sale->member->points }} pts)</p>
                        </div>
                    @endif

                    <div>
                        <span class="text-gray-400 uppercase">Cashier Staff:</span>
                        <p class="font-semibold text-gray-800 mt-0.5">{{ $sale->user?->name ?? 'POS Cashier' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Return Modal -->
        <div x-show="returnModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="returnModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-60"></div>
                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                        <h3 class="text-base font-bold text-gray-900">Process Sales Return & Refund</h3>
                        <button @click="returnModalOpen = false" class="text-gray-400 font-bold">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('sales.return', $sale) }}">
                        @csrf
                        <p class="text-xs text-gray-500 mb-4">Select items and enter return quantity. Stock will be restored and accounts adjusted automatically.</p>

                        <div class="space-y-3 max-h-60 overflow-y-auto mb-4 border border-gray-200 rounded-lg p-3">
                            @foreach ($sale->items as $item)
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Refund Method</label>
                                <select name="payment_method" class="w-full text-xs rounded-lg border-gray-300">
                                    <option value="cash">Cash Refund</option>
                                    <option value="card">Card Reversal</option>
                                    <option value="bkash">bKash</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Return Reason</label>
                                <input name="reason" placeholder="e.g. Expired, Damaged, Customer choice" class="w-full text-xs rounded-lg border-gray-300" required>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="returnModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg">Confirm & Refund</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
