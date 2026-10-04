<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Purchases & Supplier Orders</h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage supplier purchase receiving, dues, invoices, and returns.</p>
            </div>
            <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                New Purchase Order
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
            <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Search Invoice</label>
                        <input name="search" value="{{ request('search') }}" placeholder="Invoice #, Supplier..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Supplier</label>
                        <select name="supplier_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                            <option value="">All Suppliers</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                            <option value="">All Statuses</option>
                            <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-semibold transition">
                            Filter
                        </button>
                        @if (request()->hasAny(['search', 'supplier_id', 'status']))
                            <a href="{{ route('purchases.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Purchase Invoice #</th>
                            <th class="px-5 py-3.5 text-left">Supplier</th>
                            <th class="px-5 py-3.5 text-left">Date</th>
                            <th class="px-5 py-3.5 text-right">Total Amount</th>
                            <th class="px-5 py-3.5 text-right">Paid</th>
                            <th class="px-5 py-3.5 text-right">Due</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($purchases as $p)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-5 py-4 font-mono font-bold text-indigo-700">
                                    <a href="{{ route('purchases.show', $p) }}" class="hover:underline">
                                        {{ $p->purchase_invoice_number }}
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900">{{ $p->supplier?->company_name ?? '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ $p->supplier?->contact_person }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500">
                                    {{ $p->date->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-4 text-right font-black text-gray-900">
                                    ৳ {{ number_format($p->total, 2) }}
                                </td>
                                <td class="px-5 py-4 text-right font-semibold text-emerald-600">
                                    ৳ {{ number_format($p->paid, 2) }}
                                </td>
                                <td class="px-5 py-4 text-right font-semibold {{ $p->due > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    ৳ {{ number_format($p->due, 2) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        @if($p->status === 'received') bg-emerald-100 text-emerald-800
                                        @elseif($p->status === 'confirmed') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('purchases.show', $p) }}" class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded hover:bg-indigo-100 transition">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400">No purchases recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($purchases->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
