<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight flex items-center gap-2.5">
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    </span>
                    <span>Inter-Branch Stock Transfers</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Manage stock requisition, transit shipments, and warehouse receiving</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('warehouses.index') }}" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl shadow-xs transition">
                    Warehouse Stocks
                </a>
                <a href="{{ route('transfers.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Initiate New Transfer
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Transfers Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-black text-gray-900 text-base">Transfer History & Dispatch Logs</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Transfer #</th>
                                <th class="py-3.5 px-4">From Warehouse</th>
                                <th class="py-3.5 px-4">To Warehouse</th>
                                <th class="py-3.5 px-4 text-center">Items Qty</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4">Created Date</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                            @forelse ($transfers as $trf)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="py-3.5 px-4 font-mono font-bold text-gray-900">
                                        <a href="{{ route('transfers.show', $trf) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ $trf->transfer_no }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900">{{ $trf->fromWarehouse->name }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $trf->fromWarehouse->branch?->name }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900">{{ $trf->toWarehouse->name }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $trf->toWarehouse->branch?->name }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-gray-900">
                                        {{ $trf->items->sum('quantity') }} units ({{ $trf->items->count() }} SKUs)
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($trf->status === 'pending')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                                Pending Dispatch
                                            </span>
                                        @elseif ($trf->status === 'in_transit')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200 animate-pulse">
                                                In Transit
                                            </span>
                                        @elseif ($trf->status === 'received')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Received & Completed
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-gray-100 text-gray-600 border border-gray-200">
                                                Cancelled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-500">
                                        <div>{{ $trf->created_at->format('M d, Y') }}</div>
                                        <div class="text-[10px] text-gray-400">by {{ $trf->creator?->name ?? 'System' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('transfers.show', $trf) }}" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-bold rounded-lg transition">
                                                Details
                                            </a>

                                            @if ($trf->status === 'pending')
                                                <form action="{{ route('transfers.ship', $trf) }}" method="POST" onsubmit="return confirm('Confirm dispatch and reduce stock from origin warehouse?');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded-lg transition">
                                                        Ship
                                                    </button>
                                                </form>
                                            @elseif ($trf->status === 'in_transit')
                                                <form action="{{ route('transfers.receive', $trf) }}" method="POST" onsubmit="return confirm('Confirm receipt and add stock to destination warehouse?');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-lg transition">
                                                        Receive
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400 font-semibold">
                                        No stock transfers initiated yet. Click "Initiate New Transfer" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transfers->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $transfers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
