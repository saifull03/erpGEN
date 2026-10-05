<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight flex items-center gap-2.5">
                    <span class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                    </span>
                    <span>Multi-Warehouse Stock & Depots</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Real-time localized inventory quantities per warehouse location</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('branches.index') }}" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl shadow-xs transition">
                    View Branches
                </a>
                <a href="{{ route('transfers.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Transfer Stock
                </a>
                <button @click="$dispatch('open-modal', 'add-warehouse-modal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Warehouse
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Warehouse Selector Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach ($warehouses as $wh)
                    <a href="{{ route('warehouses.index', ['warehouse_id' => $wh->id]) }}" class="p-4 rounded-2xl border transition text-left flex flex-col justify-between {{ ($activeWarehouse?->id === $wh->id) ? 'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-200 shadow-sm' : 'bg-white border-gray-200 hover:border-gray-300 hover:shadow-xs' }}">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded {{ ($activeWarehouse?->id === $wh->id) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $wh->code }}
                                </span>
                                @if($wh->is_primary)
                                    <span class="text-[9px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Primary</span>
                                @endif
                            </div>
                            <h4 class="font-black text-gray-900 text-sm leading-tight mb-1">{{ $wh->name }}</h4>
                            <p class="text-[11px] text-gray-500 flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                {{ $wh->branch?->name ?? 'Unassigned' }}
                            </p>
                        </div>
                        <div class="mt-3 pt-2 border-t border-gray-100/60 flex items-center justify-between text-xs font-bold text-gray-700">
                            <span class="text-[10px] text-gray-400 font-bold uppercase">SKU Count</span>
                            <span class="font-black text-indigo-600">{{ $wh->product_stocks_count }}</span>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Active Warehouse Inventory Stock List -->
            @if ($activeWarehouse)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-gray-900 text-lg">{{ $activeWarehouse->name }}</h3>
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $activeWarehouse->code }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">Attached to Branch: <span class="font-bold text-gray-700">{{ $activeWarehouse->branch?->name }}</span> | Address: {{ $activeWarehouse->address ?? 'N/A' }}</p>
                        </div>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('warehouses.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                            <input type="hidden" name="warehouse_id" value="{{ $activeWarehouse->id }}">
                            <div class="relative w-full sm:w-64">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products in warehouse..." class="w-full pl-9 pr-4 py-2 text-xs border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                                Filter
                            </button>
                            @if(request('search'))
                                <a href="{{ route('warehouses.index', ['warehouse_id' => $activeWarehouse->id]) }}" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl transition">
                                    Clear
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4">SKU / Barcode</th>
                                    <th class="py-3 px-4">Product Name</th>
                                    <th class="py-3 px-4">Category</th>
                                    <th class="py-3 px-4">Unit</th>
                                    <th class="py-3 px-4 text-right">Selling Price</th>
                                    <th class="py-3 px-4 text-right">Warehouse Stock</th>
                                    <th class="py-3 px-4 text-center">Stock Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                @forelse ($stocks as $stock)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="py-3 px-4 font-mono font-bold text-gray-900">
                                            <div>{{ $stock->product->sku }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $stock->product->barcode }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-gray-900">{{ $stock->product->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $stock->product->brand?->name ?? 'General' }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                                {{ $stock->product->category?->name ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">{{ $stock->product->unit?->short_name ?? 'pcs' }}</td>
                                        <td class="py-3 px-4 text-right font-bold text-gray-900">৳{{ number_format($stock->product->selling_price, 2) }}</td>
                                        <td class="py-3 px-4 text-right font-black text-sm {{ $stock->stock <= 0 ? 'text-rose-600' : ($stock->stock <= $stock->product->minimum_stock ? 'text-amber-600' : 'text-emerald-600') }}">
                                            {{ $stock->stock }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($stock->stock <= 0)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">Out of Stock</span>
                                            @elseif ($stock->stock <= $stock->product->minimum_stock)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">Low Stock</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">In Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-gray-400 font-semibold">
                                            No stock records found for this warehouse or filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($stocks instanceof \Illuminate\Pagination\LengthAwarePaginator && $stocks->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $stocks->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Add Warehouse Modal -->
    <x-modal name="add-warehouse-modal" focusable>
        <form method="POST" action="{{ route('warehouses.store') }}" class="p-6">
            @csrf
            <h3 class="text-lg font-black text-gray-900 mb-4">Add New Warehouse / Stockroom</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Attached Branch</label>
                    <select name="branch_id" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Warehouse Name</label>
                    <input type="text" name="name" placeholder="e.g. Uttara Cold Storage Room" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Warehouse Code</label>
                        <input type="text" name="code" placeholder="e.g. WH-UTR01" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" placeholder="+8801700000000" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Physical Address</label>
                    <textarea name="address" rows="2" placeholder="Warehouse address details..." class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_primary" id="is_primary_new" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_primary_new" class="text-xs font-semibold text-gray-700">Set as Primary Warehouse for this branch</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">
                    Create Warehouse
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
