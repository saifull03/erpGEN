<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight flex items-center gap-2.5">
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </span>
                    <span>Transfer Note: {{ $transfer->transfer_no }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Status: <span class="font-black uppercase text-indigo-600">{{ $transfer->status }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('transfers.index') }}" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl shadow-xs transition">
                    Back to Transfers
                </a>

                @if ($transfer->status === 'pending')
                    <form action="{{ route('transfers.ship', $transfer) }}" method="POST" onsubmit="return confirm('Confirm dispatch and reduce stock from origin warehouse?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            Ship / Dispatch Transfer
                        </button>
                    </form>
                @elseif ($transfer->status === 'in_transit')
                    <form action="{{ route('transfers.receive', $transfer) }}" method="POST" onsubmit="return confirm('Confirm receipt and add stock to destination warehouse?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            Receive & Update Stock
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Route & Metadata Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Origin / Dispatch From</span>
                        <h4 class="font-black text-gray-900 text-base mt-1">{{ $transfer->fromWarehouse->name }}</h4>
                        <p class="text-xs text-indigo-600 font-bold mt-0.5">Branch: {{ $transfer->fromWarehouse->branch?->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $transfer->fromWarehouse->address ?? 'No address' }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Destination / Receiving To</span>
                        <h4 class="font-black text-gray-900 text-base mt-1">{{ $transfer->toWarehouse->name }}</h4>
                        <p class="text-xs text-indigo-600 font-bold mt-0.5">Branch: {{ $transfer->toWarehouse->branch?->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $transfer->toWarehouse->address ?? 'No address' }}</p>
                    </div>
                </div>

                @if ($transfer->notes)
                    <div class="mt-4 p-3 bg-amber-50 rounded-xl border border-amber-100 text-xs text-amber-800">
                        <span class="font-bold">Notes:</span> {{ $transfer->notes }}
                    </div>
                @endif
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-black text-gray-900 text-base">Transferred Stock Items</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4">#</th>
                                <th class="py-3 px-4">SKU / Barcode</th>
                                <th class="py-3 px-4">Product Name</th>
                                <th class="py-3 px-4 text-center">Unit</th>
                                <th class="py-3 px-4 text-right">Transfer Quantity</th>
                                <th class="py-3 px-4 text-right">Received Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                            @foreach ($transfer->items as $idx => $item)
                                <tr>
                                    <td class="py-3 px-4 text-gray-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-4 font-mono font-bold text-gray-900">{{ $item->product->sku }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-900">{{ $item->product->name }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->product->unit?->short_name ?? 'pcs' }}</td>
                                    <td class="py-3 px-4 text-right font-black text-sm text-indigo-600">{{ $item->quantity }}</td>
                                    <td class="py-3 px-4 text-right font-black text-sm {{ $item->received_quantity ? 'text-emerald-600' : 'text-gray-400' }}">
                                        {{ $item->received_quantity ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
