<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <span>User Profile &amp; Sales Dashboard</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 uppercase tracking-wide">
                        {{ $user->role?->name ?? 'Staff' }}
                    </span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Personal cashier sales analytics, transaction records, shift status, and account settings.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    POS Terminal (F2)
                </a>
                <a href="{{ route('shifts.index') }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cashier Shifts
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6"
         x-data="{
             saleModalOpen: false,
             modalSale: null,
             showSale(sale) {
                 this.modalSale = sale;
                 this.saleModalOpen = true;
             }
         }">

        <!-- User Identity & Quick Status Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl shadow-md p-6 text-white border border-slate-800">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-2xl shadow-lg ring-4 ring-white/10">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl font-black text-white">{{ $user->name }}</h1>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-500/30 text-indigo-300 border border-indigo-400/30">
                                {{ $user->role?->name ?? 'Cashier / User' }}
                            </span>
                            @if ($user->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Active
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-1.5 text-xs text-slate-300 flex-wrap">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $user->email }}
                            </span>
                            @if ($user->phone)
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $user->phone }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Branch: <strong class="text-white">{{ $user->branch?->name ?? 'Main Outlet' }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Shift Status Indicator -->
                <div class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-xl p-3.5 shrink-0">
                    <div class="p-2.5 rounded-lg {{ $activeShift ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Cash Register Shift</div>
                        @if ($activeShift)
                            <div class="text-sm font-bold text-emerald-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                Active: #{{ $activeShift->shift_number ?? 'Shift-'.$activeShift->id }}
                            </div>
                            <div class="text-[11px] text-slate-400">
                                Open Since: {{ $activeShift->opened_at ? $activeShift->opened_at->format('h:i A') : 'Today' }} • Cash: ৳{{ number_format($activeShift->cash_sales, 2) }}
                            </div>
                        @else
                            <div class="text-sm font-bold text-amber-300">No Active Shift Open</div>
                            <a href="{{ route('shifts.index') }}" class="text-[11px] text-indigo-300 hover:underline">Open a shift &rarr;</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-gray-200 overflow-x-auto gap-2 text-sm font-medium">
            <a href="{{ route('profile.edit', ['tab' => 'dashboard']) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold flex items-center gap-2 {{ $tab === 'dashboard' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Sales Performance Dashboard
            </a>
            <a href="{{ route('profile.edit', ['tab' => 'history']) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold flex items-center gap-2 {{ $tab === 'history' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                My Sales History &amp; Invoices
                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600 font-bold">{{ $totalOrders }}</span>
            </a>
            <a href="{{ route('profile.edit', ['tab' => 'account']) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold flex items-center gap-2 {{ $tab === 'account' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Account Settings &amp; Security
            </a>
        </div>

        <!-- TAB 1: SALES PERFORMANCE DASHBOARD -->
        @if ($tab === 'dashboard')
            <div class="space-y-6">
                <!-- 4 Top KPI Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Today Sales -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Today's Sales</p>
                            <span class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-black text-gray-900">৳ {{ number_format($todaySales, 2) }}</p>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                            <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">{{ $todayOrders }} invoices today</span>
                            <a href="{{ route('profile.edit', ['tab' => 'history', 'start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]) }}" class="text-indigo-600 font-bold hover:underline">View &rarr;</a>
                        </div>
                    </div>

                    <!-- This Month Sales -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">This Month's Sales</p>
                            <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-black text-gray-900">৳ {{ number_format($thisMonthSales, 2) }}</p>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                            <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">{{ $thisMonthOrders }} orders this month</span>
                            <span class="text-gray-400">{{ now()->format('F Y') }}</span>
                        </div>
                    </div>

                    <!-- Lifetime Sales Volume -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">All-Time Sales</p>
                            <span class="p-2.5 bg-purple-50 text-purple-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-black text-gray-900">৳ {{ number_format($totalSales, 2) }}</p>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                            <span class="font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md">{{ $totalOrders }} total transactions</span>
                            <a href="{{ route('profile.edit', ['tab' => 'history']) }}" class="text-purple-600 font-bold hover:underline">Full Log &rarr;</a>
                        </div>
                    </div>

                    <!-- Average Basket Size -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Average Order Value</p>
                            <span class="p-2.5 bg-amber-50 text-amber-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-2xl font-black text-gray-900">৳ {{ number_format($avgOrderValue, 2) }}</p>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                            <span>Per checkout average</span>
                            <span class="font-bold text-amber-700">{{ $totalItemsSold }} units sold</span>
                        </div>
                    </div>
                </div>

                <!-- Secondary Operational Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-xs flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-gray-400 uppercase">Items Sold</div>
                            <div class="text-base font-extrabold text-gray-900">{{ number_format($totalItemsSold) }} <span class="text-xs font-medium text-gray-500">pcs</span></div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-xs flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-gray-400 uppercase">Paid Collected</div>
                            <div class="text-base font-extrabold text-emerald-600">৳ {{ number_format($totalPaid, 2) }}</div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-xs flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-gray-400 uppercase">Discounts Granted</div>
                            <div class="text-base font-extrabold text-rose-600">৳ {{ number_format($totalDiscounts, 2) }}</div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-xs flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-gray-400 uppercase">Outstanding Dues</div>
                            <div class="text-base font-extrabold text-gray-900">৳ {{ number_format($totalDue, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- 14-Day Sales Trend & Order Volume Chart -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                        <div>
                            <h3 class="text-base font-extrabold text-gray-900">14-Day Sales Velocity &amp; Frequency</h3>
                            <p class="text-xs text-gray-500">Daily revenue and transaction count processed by {{ $user->name }}.</p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="inline-flex items-center gap-1.5 font-semibold text-indigo-700">
                                <span class="w-3 h-3 rounded-full bg-indigo-600"></span> Sales Volume (৳)
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-700">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Orders Count
                            </span>
                        </div>
                    </div>

                    <div class="h-64 sm:h-72 w-full">
                        <canvas id="personalSalesChart"></canvas>
                    </div>
                </div>

                <!-- Two Column Section: Payment Methods Breakdown & Top Sold Products -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Payment Methods Distribution -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-base font-extrabold text-gray-900 mb-1">Payment Method Distribution</h3>
                        <p class="text-xs text-gray-500 mb-5">Transactions handled by payment gateways and cash tender.</p>

                        @if ($paymentBreakdown->isEmpty())
                            <div class="text-center py-8 text-gray-400 text-sm">
                                No sales payment history recorded yet.
                            </div>
                        @else
                            <div class="space-y-4">
                                @php
                                    $paymentSum = $paymentBreakdown->sum('total_amount') ?: 1;
                                @endphp
                                @foreach ($paymentBreakdown as $pay)
                                    @php
                                        $pct = round(($pay->total_amount / $paymentSum) * 100, 1);
                                        $methodName = ucfirst(str_replace('_', ' ', $pay->payment_method));
                                        $badgeColor = match(strtolower($pay->payment_method)) {
                                            'cash' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'card' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'bkash' => 'bg-pink-50 text-pink-700 border-pink-200',
                                            'nagad' => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'rocket' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            default => 'bg-gray-50 text-gray-700 border-gray-200',
                                        };
                                        $barColor = match(strtolower($pay->payment_method)) {
                                            'cash' => 'bg-emerald-500',
                                            'card' => 'bg-blue-500',
                                            'bkash' => 'bg-pink-500',
                                            'nagad' => 'bg-orange-500',
                                            'rocket' => 'bg-purple-500',
                                            default => 'bg-indigo-500',
                                        };
                                    @endphp
                                    <div class="p-3 bg-gray-50/70 rounded-xl border border-gray-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded-md text-xs font-bold border {{ $badgeColor }}">
                                                    {{ $methodName }}
                                                </span>
                                                <span class="text-xs text-gray-500 font-medium">({{ $pay->count }} orders)</span>
                                            </div>
                                            <div class="text-sm font-extrabold text-gray-900">
                                                ৳ {{ number_format($pay->total_amount, 2) }}
                                                <span class="text-xs font-bold text-gray-400 ml-1">({{ $pct }}%)</span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Top Products Processed by this Cashier/User -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-base font-extrabold text-gray-900">Top Sold Products</h3>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">By Quantity</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-5">Items most frequently scanned and checked out by you.</p>

                        @if ($topProducts->isEmpty())
                            <div class="text-center py-8 text-gray-400 text-sm">
                                No items sold under this account yet.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($topProducts as $index => $item)
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-indigo-50/40 border border-gray-100 transition">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                                #{{ $index + 1 }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-sm text-gray-900 leading-tight">
                                                    {{ $item->product?->name ?? 'Product #'.$item->product_id }}
                                                </div>
                                                <div class="text-xs text-gray-400 font-mono">
                                                    SKU: {{ $item->product?->sku ?? 'N/A' }} • {{ $item->product?->category?->name ?? 'General' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <div class="font-black text-sm text-gray-900">{{ number_format($item->total_qty) }} <span class="text-xs font-normal text-gray-500">{{ $item->product?->unit?->short_name ?? 'pcs' }}</span></div>
                                            <div class="text-xs font-bold text-emerald-600">৳ {{ number_format($item->total_revenue, 2) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Invoices Processed -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-black text-base text-gray-900">Recent Checkout Invoices</h3>
                            <p class="text-xs text-gray-500">Latest sales orders processed at your checkout terminal.</p>
                        </div>
                        <a href="{{ route('profile.edit', ['tab' => 'history']) }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            View All Sales History &rarr;
                        </a>
                    </div>

                    @if ($recentSales->isEmpty())
                        <div class="text-center py-12 text-gray-400 text-sm">
                            No checkout sales recorded yet.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                                    <tr>
                                        <th class="px-5 py-3 text-left">Invoice #</th>
                                        <th class="px-5 py-3 text-left">Date &amp; Time</th>
                                        <th class="px-5 py-3 text-left">Customer / Member</th>
                                        <th class="px-5 py-3 text-center">Items</th>
                                        <th class="px-5 py-3 text-center">Payment</th>
                                        <th class="px-5 py-3 text-right">Grand Total</th>
                                        <th class="px-5 py-3 text-center">Status</th>
                                        <th class="px-5 py-3 text-right">Receipts</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($recentSales as $sale)
                                        <tr class="hover:bg-gray-50/70 transition">
                                            <td class="px-5 py-3.5">
                                                <button type="button" @click="showSale({{ json_encode($sale->load('items.product', 'customer', 'member')) }})" class="font-mono font-bold text-indigo-600 hover:underline">
                                                    {{ $sale->invoice_number }}
                                                </button>
                                            </td>
                                            <td class="px-5 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                                {{ $sale->created_at->format('M d, Y • h:i A') }}
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if ($sale->member)
                                                    <span class="inline-flex items-center gap-1 font-bold text-indigo-700 text-xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                                        {{ $sale->member->name }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 font-mono block">#{{ $sale->member->member_number }}</span>
                                                @elseif ($sale->customer)
                                                    <span class="font-semibold text-gray-800 text-xs">{{ $sale->customer->name }}</span>
                                                @else
                                                    <span class="text-gray-400 text-xs">Walk-in Customer</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5 text-center font-bold text-gray-700 text-xs">
                                                {{ $sale->items->count() }}
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase bg-gray-100 text-gray-700">
                                                    {{ $sale->payment_method }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-black text-gray-900 whitespace-nowrap">
                                                ৳ {{ number_format($sale->grand_total, 2) }}
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                @php
                                                    $statusClass = match($sale->status) {
                                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                        'partial' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                        'returned' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                        default => 'bg-gray-50 text-gray-700 border-gray-200',
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border uppercase {{ $statusClass }}">
                                                    {{ $sale->status }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <a href="{{ route('sales.thermal', $sale) }}" target="_blank" title="Print 80mm Thermal Receipt" class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                    </a>
                                                    <a href="{{ route('sales.invoice', $sale) }}" target="_blank" title="Print A4 Invoice" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TAB 2: MY SALES HISTORY & INVOICES -->
        @if ($tab === 'history')
            <div class="space-y-6">
                <!-- Filter Toolbar -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <form method="GET" action="{{ route('profile.edit') }}" class="space-y-4">
                        <input type="hidden" name="tab" value="history">

                        <!-- Quick Presets -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                            <span class="font-bold text-gray-500 uppercase tracking-wider text-[10px] mr-1">Presets:</span>
                            <a href="{{ route('profile.edit', ['tab' => 'history', 'start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]) }}"
                               class="px-2.5 py-1 rounded-lg border font-semibold {{ request('start_date') === now()->toDateString() && request('end_date') === now()->toDateString() ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                                Today
                            </a>
                            <a href="{{ route('profile.edit', ['tab' => 'history', 'start_date' => now()->subDay()->toDateString(), 'end_date' => now()->subDay()->toDateString()]) }}"
                               class="px-2.5 py-1 rounded-lg border font-semibold {{ request('start_date') === now()->subDay()->toDateString() && request('end_date') === now()->subDay()->toDateString() ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                                Yesterday
                            </a>
                            <a href="{{ route('profile.edit', ['tab' => 'history', 'start_date' => now()->subDays(6)->toDateString(), 'end_date' => now()->toDateString()]) }}"
                               class="px-2.5 py-1 rounded-lg border font-semibold {{ request('start_date') === now()->subDays(6)->toDateString() ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                                Last 7 Days
                            </a>
                            <a href="{{ route('profile.edit', ['tab' => 'history', 'start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->endOfMonth()->toDateString()]) }}"
                               class="px-2.5 py-1 rounded-lg border font-semibold {{ request('start_date') === now()->startOfMonth()->toDateString() ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                                This Month
                            </a>
                            <a href="{{ route('profile.edit', ['tab' => 'history']) }}"
                               class="px-2.5 py-1 rounded-lg border font-semibold {{ !request('start_date') && !request('search') && !request('payment_method') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                                All History
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Search Record</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice #, Customer Name, Phone, Member #..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">From Date</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- End Date -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">To Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Payment Method</label>
                                <select name="payment_method" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Methods</option>
                                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Card (POS)</option>
                                    <option value="bkash" {{ request('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                                    <option value="nagad" {{ request('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="rocket" {{ request('payment_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="multiple" {{ request('payment_method') === 'multiple' ? 'selected' : '' }}>Split Payment</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <div class="text-xs text-gray-500 font-medium">
                                Showing <strong>{{ $filteredCount }}</strong> sales records • Total Volume: <strong class="text-gray-900 font-bold">৳ {{ number_format($filteredTotal, 2) }}</strong>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('profile.edit', ['tab' => 'history']) }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition">
                                    Clear
                                </a>
                                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                    Filter History
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Sales History Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    @if ($salesHistory->isEmpty())
                        <div class="text-center py-16 px-4">
                            <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-700">No Sales Records Found</h4>
                            <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or date range.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                                    <tr>
                                        <th class="px-5 py-3 text-left">Invoice #</th>
                                        <th class="px-5 py-3 text-left">Date &amp; Time</th>
                                        <th class="px-5 py-3 text-left">Customer / Member</th>
                                        <th class="px-5 py-3 text-center">Items</th>
                                        <th class="px-5 py-3 text-center">Payment</th>
                                        <th class="px-5 py-3 text-right">Grand Total</th>
                                        <th class="px-5 py-3 text-right">Paid</th>
                                        <th class="px-5 py-3 text-right">Due</th>
                                        <th class="px-5 py-3 text-center">Status</th>
                                        <th class="px-5 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($salesHistory as $sale)
                                        <tr class="hover:bg-indigo-50/30 transition">
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <button type="button" @click="showSale({{ json_encode($sale) }})" class="font-mono font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                                    {{ $sale->invoice_number }}
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                @if ($sale->shift)
                                                    <span class="text-[10px] text-gray-400 font-mono block">Shift #{{ $sale->shift->shift_number ?? $sale->shift->id }}</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                                <div>{{ $sale->created_at->format('M d, Y') }}</div>
                                                <div class="text-gray-400 text-[11px]">{{ $sale->created_at->format('h:i:s A') }}</div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if ($sale->member)
                                                    <div class="font-bold text-indigo-700 text-xs flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                                        {{ $sale->member->name }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 font-mono">#{{ $sale->member->member_number }}</div>
                                                @elseif ($sale->customer)
                                                    <div class="font-semibold text-gray-800 text-xs">{{ $sale->customer->name }}</div>
                                                    @if ($sale->customer->phone)
                                                        <div class="text-[10px] text-gray-400">{{ $sale->customer->phone }}</div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">Walk-in Customer</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">
                                                    {{ $sale->items->count() }} items
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                                @php
                                                    $badgeColor = match(strtolower($sale->payment_method)) {
                                                        'cash' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                        'card' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                        'bkash' => 'bg-pink-50 text-pink-700 border-pink-200',
                                                        'nagad' => 'bg-orange-50 text-orange-700 border-orange-200',
                                                        default => 'bg-gray-50 text-gray-700 border-gray-200',
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold uppercase border {{ $badgeColor }}">
                                                    {{ $sale->payment_method }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-black text-gray-900 whitespace-nowrap">
                                                ৳ {{ number_format($sale->grand_total, 2) }}
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-medium text-emerald-600 whitespace-nowrap text-xs">
                                                ৳ {{ number_format($sale->paid_amount, 2) }}
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-medium {{ $sale->due_amount > 0 ? 'text-rose-600 font-bold' : 'text-gray-400' }} whitespace-nowrap text-xs">
                                                ৳ {{ number_format($sale->due_amount, 2) }}
                                            </td>
                                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                                @php
                                                    $statusClass = match($sale->status) {
                                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                        'partial' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                        'returned' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                        default => 'bg-gray-50 text-gray-700 border-gray-200',
                                                    };
                                                @endphp
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase {{ $statusClass }}">
                                                    {{ $sale->status }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <button type="button" @click="showSale({{ json_encode($sale) }})" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition" title="Quick View">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </button>
                                                    <a href="{{ route('sales.thermal', $sale) }}" target="_blank" title="Print 80mm Thermal Receipt" class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                    </a>
                                                    <a href="{{ route('sales.invoice', $sale) }}" target="_blank" title="Print A4 Invoice" class="p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    </a>
                                                    <a href="{{ route('sales.show', $sale) }}" class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition" title="Sale Details & Return Page">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="p-4 border-t border-gray-100">
                            {{ $salesHistory->links() }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- TAB 3: ACCOUNT SETTINGS & SECURITY -->
        @if ($tab === 'account')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Update Profile Info -->
                    <div class="p-6 bg-white shadow-sm border border-gray-100 rounded-2xl">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="p-6 bg-white shadow-sm border border-gray-100 rounded-2xl">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="p-6 bg-white shadow-sm border border-gray-100 rounded-2xl">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <!-- Account & Role Details Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <h3 class="font-black text-sm uppercase tracking-wider text-gray-900 pb-2 border-b border-gray-100">
                            System Authorization &amp; Branch
                        </h3>
                        
                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-gray-400 font-medium">Assigned Role:</span>
                                <div class="font-bold text-gray-900 mt-0.5 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    {{ $user->role?->name ?? 'User' }}
                                </div>
                            </div>

                            <div>
                                <span class="text-gray-400 font-medium">Assigned Outlet / Branch:</span>
                                <div class="font-bold text-gray-900 mt-0.5">
                                    {{ $user->branch?->name ?? 'Main Outlet' }}
                                    @if ($user->branch?->code)
                                        <span class="text-gray-400 font-mono">({{ $user->branch->code }})</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <span class="text-gray-400 font-medium">Account Status:</span>
                                <div class="mt-0.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                        {{ $user->status ?? 'active' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="text-gray-400 font-medium">Member Since:</span>
                                <div class="font-bold text-gray-900 mt-0.5">
                                    {{ $user->created_at ? $user->created_at->format('F d, Y') : 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 text-[11px] text-gray-500">
                            Role and branch allocations are managed centrally by the Super Administrator.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick View Sale Modal -->
        <div x-show="saleModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" @click="saleModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100"
                     @click.outside="saleModalOpen = false">
                    
                    <template x-if="modalSale">
                        <div>
                            <!-- Modal Header -->
                            <div class="bg-gray-900 text-white p-5 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-bold bg-indigo-500/30 text-indigo-200 px-2 py-0.5 rounded border border-indigo-400/30">
                                            Invoice #<span x-text="modalSale.invoice_number"></span>
                                        </span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-emerald-500/20 text-emerald-300" x-text="modalSale.status"></span>
                                    </div>
                                    <p class="text-xs text-slate-300 mt-1" x-text="new Date(modalSale.created_at).toLocaleString()"></p>
                                </div>
                                <button type="button" @click="saleModalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                                <!-- Customer Info -->
                                <div class="p-3 bg-gray-50 rounded-xl flex items-center justify-between text-xs">
                                    <div>
                                        <span class="text-gray-400 font-medium">Customer:</span>
                                        <div class="font-bold text-gray-900 mt-0.5" x-text="modalSale.member ? modalSale.member.name + ' (Member #' + modalSale.member.member_number + ')' : (modalSale.customer ? modalSale.customer.name : 'Walk-in Customer')"></div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-gray-400 font-medium">Payment Tender:</span>
                                        <div class="font-bold text-gray-900 uppercase mt-0.5" x-text="modalSale.payment_method"></div>
                                    </div>
                                </div>

                                <!-- Items List -->
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Purchased Items</h4>
                                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-100 text-xs">
                                            <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px]">
                                                <tr>
                                                    <th class="px-3 py-2 text-left">Item</th>
                                                    <th class="px-3 py-2 text-center">Qty</th>
                                                    <th class="px-3 py-2 text-right">Unit Price</th>
                                                    <th class="px-3 py-2 text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                <template x-for="item in modalSale.items" :key="item.id">
                                                    <tr>
                                                        <td class="px-3 py-2">
                                                            <div class="font-bold text-gray-900" x-text="item.product ? item.product.name : 'Item #' + item.product_id"></div>
                                                            <div class="text-[10px] text-gray-400 font-mono" x-text="item.product ? item.product.sku : ''"></div>
                                                        </td>
                                                        <td class="px-3 py-2 text-center font-bold text-gray-700" x-text="item.quantity"></td>
                                                        <td class="px-3 py-2 text-right text-gray-600 font-mono" x-text="'৳ ' + Number(item.unit_price).toFixed(2)"></td>
                                                        <td class="px-3 py-2 text-right font-bold text-gray-900 font-mono" x-text="'৳ ' + Number(item.subtotal).toFixed(2)"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Financial Totals -->
                                <div class="space-y-1.5 text-xs text-gray-600 bg-gray-50 p-4 rounded-xl">
                                    <div class="flex justify-between">
                                        <span>Subtotal</span>
                                        <span class="font-mono font-bold text-gray-900" x-text="'৳ ' + Number(modalSale.subtotal || 0).toFixed(2)"></span>
                                    </div>
                                    <template x-if="Number(modalSale.discount_amount || 0) > 0">
                                        <div class="flex justify-between text-rose-600">
                                            <span>Discount</span>
                                            <span class="font-mono font-bold" x-text="'- ৳ ' + Number(modalSale.discount_amount).toFixed(2)"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between">
                                        <span>Tax / VAT</span>
                                        <span class="font-mono text-gray-900" x-text="'৳ ' + Number(modalSale.tax_amount || 0).toFixed(2)"></span>
                                    </div>
                                    <div class="flex justify-between text-sm font-black text-gray-900 pt-2 border-t border-gray-200">
                                        <span>Grand Total</span>
                                        <span class="text-indigo-700" x-text="'৳ ' + Number(modalSale.grand_total || 0).toFixed(2)"></span>
                                    </div>
                                    <div class="flex justify-between text-emerald-600 font-bold pt-1">
                                        <span>Paid Amount</span>
                                        <span x-text="'৳ ' + Number(modalSale.paid_amount || 0).toFixed(2)"></span>
                                    </div>
                                    <template x-if="Number(modalSale.due_amount || 0) > 0">
                                        <div class="flex justify-between text-rose-600 font-bold">
                                            <span>Due Amount</span>
                                            <span x-text="'৳ ' + Number(modalSale.due_amount).toFixed(2)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="bg-gray-50 p-4 border-t border-gray-100 flex items-center justify-between">
                                <a :href="'/sales/' + modalSale.id" class="text-xs font-bold text-indigo-600 hover:underline">
                                    Open Full Sale Record &rarr;
                                </a>
                                <div class="flex items-center gap-2">
                                    <a :href="'/sales/' + modalSale.id + '/thermal'" target="_blank" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Thermal (80mm)
                                    </a>
                                    <a :href="'/sales/' + modalSale.id + '/invoice'" target="_blank" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        A4 Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script for Dashboard -->
    @if ($tab === 'dashboard')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('personalSalesChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($chartLabels) !!},
                            datasets: [
                                {
                                    type: 'line',
                                    label: 'Daily Sales Volume (৳)',
                                    data: {!! json_encode($chartSalesData) !!},
                                    borderColor: '#4f46e5',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.35,
                                    yAxisID: 'y',
                                    pointBackgroundColor: '#4f46e5',
                                    pointRadius: 4,
                                },
                                {
                                    type: 'bar',
                                    label: 'Orders Count',
                                    data: {!! json_encode($chartOrdersData) !!},
                                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                    borderRadius: 6,
                                    barThickness: 16,
                                    yAxisID: 'y1',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    titleFont: { size: 12, weight: 'bold' },
                                    bodyFont: { size: 12 },
                                    padding: 10,
                                    callbacks: {
                                        label: function(context) {
                                            if (context.dataset.yAxisID === 'y') {
                                                return ' Sales: ৳ ' + Number(context.raw).toLocaleString('en-US', { minimumFractionDigits: 2 });
                                            }
                                            return ' Invoices: ' + context.raw;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: { size: 11 }
                                    }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    grid: {
                                        color: '#f1f5f9'
                                    },
                                    ticks: {
                                        font: { size: 11 },
                                        callback: function(value) {
                                            return '৳ ' + value.toLocaleString();
                                        }
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false
                                    },
                                    ticks: {
                                        font: { size: 11 },
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
