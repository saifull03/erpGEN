<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Customer Ledger: {{ $customer->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Customer ID: <span class="font-mono font-bold">{{ $customer->customer_id }}</span> | Phone: {{ $customer->phone ?? '—' }}</p>
            </div>
            <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">
                Back to Customers
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Balance Header -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase text-gray-400">Current Customer Balance</span>
                <p class="text-3xl font-black text-indigo-700 mt-1">৳ {{ number_format($customer->current_balance, 2) }}</p>
            </div>
            <div class="text-xs text-gray-500 space-y-1">
                <div>Total POS Purchases: <span class="font-bold text-gray-800">{{ $customer->sales->count() }} Invoices</span></div>
                <div>Opening Balance: <span class="font-bold text-gray-800">৳ {{ number_format($customer->opening_balance, 2) }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-800 text-sm">
                Transaction History & Ledger Entries
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Date</th>
                        <th class="px-5 py-3.5 text-left">Reference #</th>
                        <th class="px-5 py-3.5 text-left">Description</th>
                        <th class="px-5 py-3.5 text-right">Debit (Purchased)</th>
                        <th class="px-5 py-3.5 text-right">Credit (Paid)</th>
                        <th class="px-5 py-3.5 text-right">Running Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($entries as $e)
                        <tr>
                            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $e->date->format('M d, Y') }}</td>
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-indigo-700">{{ $e->reference }}</td>
                            <td class="px-5 py-3.5 text-gray-800 text-xs">{{ $e->description }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-gray-900">৳ {{ number_format($e->debit, 2) }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-emerald-600">৳ {{ number_format($e->credit, 2) }}</td>
                            <td class="px-5 py-3.5 text-right font-black text-indigo-700">৳ {{ number_format($e->balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No ledger transactions recorded for this customer.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if ($entries->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
