<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    OneStop Supermarket Dashboard
                </h2>
                <p class="text-sm text-gray-500 mt-1">Daily operations overview, live sales, inventory health, and cash status.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Open POS Terminal (F2)
                </a>
                <a href="{{ route('shifts.index') }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Shifts & Cash
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Today Sales -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Today's Sales</p>
                    <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-gray-900">৳ {{ number_format($todaySales ?? 0, 2) }}</p>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                    <span>{{ $todayTransactions ?? 0 }} orders completed</span>
                    <a href="{{ route('sales.index') }}" class="text-emerald-600 font-medium hover:underline">View Sales &rarr;</a>
                </div>
            </div>

            <!-- Today Purchases -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Today's Purchases</p>
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-gray-900">৳ {{ number_format($todayPurchases ?? 0, 2) }}</p>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                    <span>Inventory procurement</span>
                    <a href="{{ route('purchases.index') }}" class="text-blue-600 font-medium hover:underline">Purchases &rarr;</a>
                </div>
            </div>

            <!-- Today Expenses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Today's Expenses</p>
                    <span class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-gray-900">৳ {{ number_format($todayExpenses ?? 0, 2) }}</p>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                    <span>Operational outflows</span>
                    <a href="{{ route('expenses.index') }}" class="text-amber-600 font-medium hover:underline">Expenses &rarr;</a>
                </div>
            </div>

            <!-- Today Profit (COGS-based) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Today's Net Profit</p>
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-extrabold {{ ($todayProfit ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    ৳ {{ number_format($todayProfit ?? 0, 2) }}
                </p>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                    <span>(Sales - COGS - Exp)</span>
                    <a href="{{ route('reports.index', ['tab' => 'profit_loss']) }}" class="text-indigo-600 font-medium hover:underline">P&L Report &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Secondary Highlights: Cash in Hand & Operational Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-indigo-900 to-indigo-800 text-white rounded-xl p-5 shadow-sm">
                <div class="text-xs uppercase font-medium text-indigo-200">Open Shifts Cash in Hand</div>
                <div class="mt-2 text-2xl font-black">৳ {{ number_format($cashInHand ?? 0, 2) }}</div>
                <div class="mt-2 text-xs text-indigo-200 flex items-center justify-between">
                    <span>Active Cash Registers</span>
                    <a href="{{ route('shifts.index') }}" class="text-white underline font-semibold">View Register</a>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase font-semibold text-gray-400">Total Products</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalProducts ?? 0 }}</div>
                    <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 hover:underline">Browse Catalog &rarr;</a>
                </div>
                <div class="p-3 bg-gray-50 text-gray-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase font-semibold text-gray-400">Registered Members</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalMembers ?? 0 }}</div>
                    <div class="text-xs text-gray-500">From {{ $totalCustomers ?? 0 }} total customers</div>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase font-semibold text-gray-400">Total Suppliers</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalSuppliers ?? 0 }}</div>
                    <a href="{{ route('suppliers.index') }}" class="text-xs text-indigo-600 hover:underline">View Suppliers &rarr;</a>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
        </div>

        <!-- Inventory Alerts & Quick Operations -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Stock Alerts -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 text-base flex items-center">
                        <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Stock & Expiry Alerts
                    </h3>
                    <a href="{{ route('inventory.alerts') }}" class="text-xs text-indigo-600 font-semibold hover:underline">View All &rarr;</a>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-4 text-center">
                    <div class="p-3 rounded-lg bg-red-50 border border-red-100">
                        <div class="text-xl font-bold text-red-600">{{ $outOfStockProducts ?? 0 }}</div>
                        <div class="text-xs text-red-700 font-medium">Out of Stock</div>
                    </div>
                    <div class="p-3 rounded-lg bg-amber-50 border border-amber-100">
                        <div class="text-xl font-bold text-amber-600">{{ $lowStockProducts ?? 0 }}</div>
                        <div class="text-xs text-amber-700 font-medium">Low Stock</div>
                    </div>
                    <div class="p-3 rounded-lg bg-purple-50 border border-purple-100">
                        <div class="text-xl font-bold text-purple-600">{{ $expiringProducts ?? 0 }}</div>
                        <div class="text-xs text-purple-700 font-medium">Expiring Soon</div>
                    </div>
                </div>

                <div class="space-y-2.5">
                    @forelse ($criticalLowStock ?? [] as $item)
                        <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg text-sm">
                            <div class="truncate pr-2">
                                <div class="font-medium text-gray-900 truncate">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $item->sku }}</div>
                            </div>
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $item->current_stock <= 0 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $item->current_stock }} in stock
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-gray-400">No critical stock warnings right now.</div>
                    @endforelse
                </div>
            </div>

            <!-- Middle & Right: Recent Sales Stream -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 text-base flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Recent POS Transactions
                    </h3>
                    <a href="{{ route('sales.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline">View All Sales &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-3 py-2.5 text-left">Invoice</th>
                                <th class="px-3 py-2.5 text-left">Customer</th>
                                <th class="px-3 py-2.5 text-left">Cashier</th>
                                <th class="px-3 py-2.5 text-right">Amount</th>
                                <th class="px-3 py-2.5 text-right">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentSales ?? [] as $sale)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-3 py-3">
                                        <a href="{{ route('sales.show', $sale) }}" class="font-mono font-bold text-indigo-600 hover:underline">
                                            {{ $sale->invoice_number }}
                                        </a>
                                        <div class="text-[11px] text-gray-400">{{ $sale->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-gray-700">
                                        <div class="font-medium">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</div>
                                        @if ($sale->member_id)
                                            <span class="inline-flex items-center text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">Member</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-500">
                                        {{ $sale->user?->name ?? 'Cashier' }}
                                    </td>
                                    <td class="px-3 py-3 text-right font-bold text-gray-900">
                                        ৳ {{ number_format($sale->grand_total, 2) }}
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('sales.thermal', $sale) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded transition">
                                            Thermal
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-8 text-center text-xs text-gray-400">No sales completed yet today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Launch Module Grid -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Quick Navigation & Master Data</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                <a href="{{ route('products.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition group text-center">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-900">Add Product</span>
                </a>
                <a href="{{ route('purchases.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition group text-center">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-900">New Purchase</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition group text-center">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7c0-2 1.5-3 3.5-3h9c2 0 3.5 1 3.5 3M4 7h16"/></svg>
                    <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-900">Stock & Adjust</span>
                </a>
                <a href="{{ route('members.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition group text-center">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-900">Memberships</span>
                </a>
                <a href="{{ route('accounts.ledger') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition group text-center">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-900">Unified Ledger</span>
                </a>
                <a href="{{ route('reports.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition group text-center">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-900">ERP Reports</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
