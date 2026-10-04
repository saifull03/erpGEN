<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Unified Financial Ledger</h2>
                <p class="text-sm text-gray-500 mt-1">Double-entry transaction records across customers, suppliers, sales, purchases, and expenses.</p>
            </div>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Ledger
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Summary Financial Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Inflow / Debits</p>
                <p class="mt-2 text-2xl font-extrabold text-emerald-600">৳ {{ number_format($totalDebit ?? 0, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">Customer receivables & cash inflows</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Outflow / Credits</p>
                <p class="mt-2 text-2xl font-extrabold text-rose-600">৳ {{ number_format($totalCredit ?? 0, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">Supplier settlements & operational overhead</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Net Ledger Variance</p>
                <p class="mt-2 text-2xl font-extrabold text-indigo-900">৳ {{ number_format(($totalDebit ?? 0) - ($totalCredit ?? 0), 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">Filtered balance discrepancy</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4">
            <form method="GET" action="{{ route('accounts.ledger') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Ledger Type</label>
                    <select name="ledger_type" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Accounts</option>
                        <option value="customer" {{ request('ledger_type') === 'customer' ? 'selected' : '' }}>Customer Receivables</option>
                        <option value="supplier" {{ request('ledger_type') === 'supplier' ? 'selected' : '' }}>Supplier Payables</option>
                        <option value="sales" {{ request('ledger_type') === 'sales' ? 'selected' : '' }}>Sales Revenue</option>
                        <option value="purchase" {{ request('ledger_type') === 'purchase' ? 'selected' : '' }}>Purchases</option>
                        <option value="expense" {{ request('ledger_type') === 'expense' ? 'selected' : '' }}>Expenses</option>
                        <option value="cash" {{ request('ledger_type') === 'cash' ? 'selected' : '' }}>Cash & Bank</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Search Ref/Description</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice / Ref / Notes" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['ledger_type', 'start_date', 'end_date', 'search']))
                        <a href="{{ route('accounts.ledger') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Ledger Entries Table -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-base">General Transaction Entries</h3>
                <span class="text-xs text-gray-500">{{ $entries->total() ?? count($entries) }} entries recorded</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Date / Time</th>
                            <th class="px-5 py-3.5 text-left">Account Type</th>
                            <th class="px-5 py-3.5 text-left">Reference</th>
                            <th class="px-5 py-3.5 text-left">Description</th>
                            <th class="px-5 py-3.5 text-right text-emerald-700">Debit (৳)</th>
                            <th class="px-5 py-3.5 text-right text-rose-700">Credit (৳)</th>
                            <th class="px-5 py-3.5 text-right">Balance (৳)</th>
                            <th class="px-5 py-3.5 text-left">Logged By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($entries as $e)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($e->date)->format('M d, Y') }}
                                    <div class="text-[11px] text-gray-400">{{ $e->created_at ? $e->created_at->format('h:i A') : '' }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium uppercase font-mono
                                        {{ $e->ledger_type === 'sales' ? 'bg-emerald-100 text-emerald-800' :
                                           ($e->ledger_type === 'purchase' ? 'bg-blue-100 text-blue-800' :
                                           ($e->ledger_type === 'expense' ? 'bg-amber-100 text-amber-800' :
                                           ($e->ledger_type === 'customer' ? 'bg-indigo-100 text-indigo-800' :
                                           ($e->ledger_type === 'supplier' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')))) }}">
                                        {{ $e->ledger_type }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs font-bold text-gray-800">
                                    {{ $e->reference ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-800 max-w-sm">
                                    <div class="text-sm font-medium">{{ $e->description }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold {{ $e->debit > 0 ? 'text-emerald-600' : 'text-gray-300' }}">
                                    {{ $e->debit > 0 ? '৳ ' . number_format($e->debit, 2) : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold {{ $e->credit > 0 ? 'text-rose-600' : 'text-gray-300' }}">
                                    {{ $e->credit > 0 ? '৳ ' . number_format($e->credit, 2) : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-extrabold text-gray-900">
                                    ৳ {{ number_format($e->running_balance, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $e->user?->name ?? 'System' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-gray-400">No ledger entries matching query.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($entries->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
