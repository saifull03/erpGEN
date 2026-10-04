<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Stock & Expiry Alerts</h2>
                <p class="text-sm text-gray-500 mt-0.5">Critical inventory alerts: low stock, out of stock, and near-expiry goods.</p>
            </div>
            <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">
                Stock Movements
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ tab: 'low' }">
        <!-- Tab Navigation Buttons -->
        <div class="flex items-center gap-2 border-b border-gray-200 pb-3">
            <button @click="tab = 'low'" :class="tab === 'low' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 border border-gray-200">
                <span>Low Stock</span>
                <span class="px-1.5 py-0.2 bg-white/20 rounded-full text-[10px]">{{ $lowStock->count() }}</span>
            </button>
            <button @click="tab = 'out'" :class="tab === 'out' ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 border border-gray-200">
                <span>Out of Stock</span>
                <span class="px-1.5 py-0.2 bg-white/20 rounded-full text-[10px]">{{ $outOfStock->count() }}</span>
            </button>
            <button @click="tab = 'expiring'" :class="tab === 'expiring' ? 'bg-purple-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 border border-gray-200">
                <span>Expiring Soon (< 30 Days)</span>
                <span class="px-1.5 py-0.2 bg-white/20 rounded-full text-[10px]">{{ $expiring30Days->count() }}</span>
            </button>
        </div>

        <!-- Low Stock Table -->
        <div x-show="tab === 'low'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-amber-50/60 border-b border-amber-100 font-bold text-amber-900 text-sm">
                Products with Stock at or below Minimum Threshold
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-5 py-3 text-left">Product</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-center">Current Stock</th>
                        <th class="px-5 py-3 text-center">Min Level</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($lowStock as $p)
                        <tr>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-gray-900">{{ $p->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $p->sku }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-gray-600">{{ $p->category?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center font-black text-amber-600">{{ $p->current_stock }} {{ $p->unit?->short_name }}</td>
                            <td class="px-5 py-3.5 text-center font-semibold text-gray-400">{{ $p->minimum_stock }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('purchases.create') }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-md">Order Stock</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">All products have healthy stock levels.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Out of Stock Table -->
        <div x-show="tab === 'out'" style="display: none;" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-red-50/60 border-b border-red-100 font-bold text-red-900 text-sm">
                Products completely Out of Stock (0 Units)
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-5 py-3 text-left">Product</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-center">Stock</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($outOfStock as $p)
                        <tr>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-gray-900">{{ $p->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $p->sku }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-gray-600">{{ $p->category?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center font-black text-red-600">0 {{ $p->unit?->short_name }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('purchases.create') }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-md">Create Purchase Order</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No out of stock items.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Expiring Goods Table -->
        <div x-show="tab === 'expiring'" style="display: none;" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-purple-50/60 border-b border-purple-100 font-bold text-purple-900 text-sm">
                Goods Expiring within 30 Days
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-5 py-3 text-left">Product</th>
                        <th class="px-5 py-3 text-center">Current Stock</th>
                        <th class="px-5 py-3 text-left">Expiry Date</th>
                        <th class="px-5 py-3 text-center">Days Remaining</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($expiring30Days as $p)
                        @php $days = (int) now()->diffInDays($p->expiry_date, false); @endphp
                        <tr>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-gray-900">{{ $p->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $p->sku }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-gray-800">{{ $p->current_stock }} {{ $p->unit?->short_name }}</td>
                            <td class="px-5 py-3.5 text-xs font-semibold text-gray-700">{{ $p->expiry_date->format('M d, Y') }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $days < 0 ? 'bg-red-100 text-red-800' : ($days <= 7 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $days < 0 ? 'EXPIRED' : $days . ' Days' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No products expiring in the next 30 days.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
