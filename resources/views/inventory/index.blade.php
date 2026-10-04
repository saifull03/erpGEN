<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Inventory & Stock Tracking</h2>
                <p class="text-sm text-gray-500 mt-0.5">Audit every stock in, stock out, damage, and adjustment across the supermarket.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory.alerts') }}" class="px-3 py-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold rounded-lg hover:bg-amber-100 transition">
                    Stock & Expiry Alerts
                </a>
                <button type="button" @click="$dispatch('open-stock-adjust-modal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                    + Stock Adjustment
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ adjustModalOpen: false }" @open-stock-adjust-modal.window="adjustModalOpen = true">
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm text-emerald-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Valuation Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold uppercase text-gray-400">Total Stock Cost Value</span>
                <p class="text-2xl font-black text-gray-900 mt-1">৳ {{ number_format($valuation['total_cost_value'], 2) }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold uppercase text-gray-400">Total Retail Value</span>
                <p class="text-2xl font-black text-indigo-700 mt-1">৳ {{ number_format($valuation['total_retail_value'], 2) }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold uppercase text-gray-400">Potential Gross Profit</span>
                <p class="text-2xl font-black text-emerald-600 mt-1">৳ {{ number_format($valuation['estimated_profit'], 2) }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-xs font-bold uppercase text-gray-400">Total Units in Store</span>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($valuation['total_units_in_stock']) }}</p>
            </div>
        </div>

        <!-- Stock Movements Ledger Table -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Search Reference / SKU</label>
                        <input name="search" value="{{ request('search') }}" placeholder="Reference #, Product..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Movement Type</label>
                        <select name="type" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                            <option value="">All Types</option>
                            <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale (POS)</option>
                            <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Purchase In</option>
                            <option value="sale_return" {{ request('type') === 'sale_return' ? 'selected' : '' }}>Sale Return</option>
                            <option value="purchase_return" {{ request('type') === 'purchase_return' ? 'selected' : '' }}>Purchase Return</option>
                            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            <option value="damage" {{ request('type') === 'damage' ? 'selected' : '' }}>Damage</option>
                            <option value="expired" {{ request('type') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Product</label>
                        <select name="product_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                            <option value="">All Products</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-semibold transition">
                            Filter
                        </button>
                        @if (request()->hasAny(['search', 'type', 'product_id']))
                            <a href="{{ route('inventory.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Date & Time</th>
                            <th class="px-5 py-3.5 text-left">Product</th>
                            <th class="px-5 py-3.5 text-center">Type</th>
                            <th class="px-5 py-3.5 text-center">Change Qty</th>
                            <th class="px-5 py-3.5 text-right">Prev Stock</th>
                            <th class="px-5 py-3.5 text-right">New Stock</th>
                            <th class="px-5 py-3.5 text-left">Reference / Notes</th>
                            <th class="px-5 py-3.5 text-left">Staff</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($movements as $m)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-5 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $m->created_at->format('M d, Y • h:i A') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-gray-900">{{ $m->product?->name }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $m->product?->sku }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                                        @if($m->type === 'sale') bg-blue-100 text-blue-800
                                        @elseif($m->type === 'purchase') bg-emerald-100 text-emerald-800
                                        @elseif($m->type === 'sale_return') bg-purple-100 text-purple-800
                                        @elseif($m->type === 'damage' || $m->type === 'expired') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $m->type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center font-black text-sm {{ $m->quantity > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                                </td>
                                <td class="px-5 py-3.5 text-right text-gray-500">{{ $m->previous_stock }}</td>
                                <td class="px-5 py-3.5 text-right font-black text-gray-900">{{ $m->new_stock }}</td>
                                <td class="px-5 py-3.5 text-xs text-gray-600">
                                    <div class="font-mono font-semibold">{{ $m->reference }}</div>
                                    <div class="text-gray-400 truncate max-w-xs">{{ $m->notes }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $m->user?->name ?? 'System' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400">No stock movements recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>

        <!-- Stock Adjustment Modal -->
        <div x-show="adjustModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="adjustModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-60"></div>
                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-lg">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                        <h3 class="text-base font-bold text-gray-900">Manual Stock Adjustment</h3>
                        <button @click="adjustModalOpen = false" class="text-gray-400 font-bold">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('inventory.adjust') }}">
                        @csrf
                        <div class="space-y-3.5 text-sm">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Select Product *</label>
                                <select name="product_id" class="w-full rounded-lg border-gray-300 text-sm" required>
                                    <option value="">-- Choose Product --</option>
                                    @foreach ($products as $prd)
                                        <option value="{{ $prd->id }}">{{ $prd->name }} (Current: {{ $prd->current_stock }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">New Stock Count *</label>
                                    <input name="new_stock" type="number" min="0" placeholder="e.g. 50" class="w-full rounded-lg border-gray-300 text-sm font-bold" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Adjustment Type</label>
                                    <select name="type" class="w-full rounded-lg border-gray-300 text-sm">
                                        <option value="adjustment">Count Correction</option>
                                        <option value="damage">Damaged Goods</option>
                                        <option value="expired">Expired Stock</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Reason / Note *</label>
                                <input name="reason" placeholder="e.g. Physical inventory count, damaged bottle" class="w-full rounded-lg border-gray-300 text-sm" required>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="adjustModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm">Save Adjustment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
