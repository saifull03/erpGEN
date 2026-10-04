<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Sales & Invoices</h2>
                <p class="text-sm text-gray-500 mt-0.5">View all completed sales transactions, invoices, customer receipts, and process returns.</p>
            </div>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                New POS Sale
            </a>
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
                <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Search</label>
                        <input name="search" value="{{ request('search') }}" placeholder="Invoice #, Customer, Member..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">From Date</label>
                        <input name="start_date" type="date" value="{{ request('start_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">To Date</label>
                        <input name="end_date" type="date" value="{{ request('end_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-semibold transition">
                            Filter
                        </button>
                        @if (request()->hasAny(['search', 'start_date', 'end_date']))
                            <a href="{{ route('sales.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Invoices Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Invoice #</th>
                            <th class="px-5 py-3.5 text-left">Customer / Member</th>
                            <th class="px-5 py-3.5 text-left">Cashier</th>
                            <th class="px-5 py-3.5 text-right">Grand Total</th>
                            <th class="px-5 py-3.5 text-right">Paid</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-left">Date & Time</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-5 py-4 font-mono font-bold text-indigo-700">
                                    <a href="{{ route('sales.show', $sale) }}" class="hover:underline">
                                        {{ $sale->invoice_number }}
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($sale->member)
                                        <div class="font-bold text-gray-900">{{ $sale->member->name }}</div>
                                        <span class="text-[10px] font-mono bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-200 font-semibold">Card: {{ $sale->member->member_number }}</span>
                                    @elseif ($sale->customer)
                                        <div class="font-medium text-gray-900">{{ $sale->customer->name }}</div>
                                        <span class="text-xs text-gray-400 font-mono">{{ $sale->customer->customer_id }}</span>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">Walk-in Customer</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">
                                    {{ $sale->user?->name ?? 'POS Cashier' }}
                                </td>
                                <td class="px-5 py-4 text-right font-black text-gray-900">
                                    ৳ {{ number_format($sale->grand_total, 2) }}
                                </td>
                                <td class="px-5 py-4 text-right font-semibold text-emerald-600">
                                    ৳ {{ number_format($sale->paid_amount, 2) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        @if($sale->status === 'completed') bg-emerald-100 text-emerald-800
                                        @elseif($sale->status === 'partial') bg-amber-100 text-amber-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500">
                                    {{ $sale->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('sales.show', $sale) }}" class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded hover:bg-indigo-100 transition">
                                            View Details
                                        </a>
                                        <a href="{{ route('sales.thermal', $sale) }}" target="_blank" class="p-1 text-gray-600 hover:text-gray-900" title="Thermal Print">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    No sales transactions recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sales->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
