@php
    $isCashier = Auth::user()?->hasRole('cashier');
    $isManagerOverridden = session('manager_authorized_until', 0) > time();
    $overrideRemaining = $isManagerOverridden ? max(1, (int) ceil((session('manager_authorized_until') - time()) / 60)) : 0;
    $managerName = session('authorized_by_manager_name', 'Branch Manager');
    $allBranches = \App\Models\Branch::where('status', 'active')->get();
    $activeBranchId = session('active_branch_id');
    $activeBranchObj = $activeBranchId ? $allBranches->firstWhere('id', $activeBranchId) : null;
@endphp

@if ($isCashier && $isManagerOverridden)
    <div class="bg-amber-500 text-amber-950 px-4 py-1.5 text-xs font-black flex items-center justify-between shadow-xs sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center px-2 py-0.5 bg-amber-900 text-amber-100 rounded text-[10px] font-black uppercase tracking-wider">Manager Mode</span>
            <span>Unlocked by <strong>{{ $managerName }}</strong> &bull; Access expires in <strong>{{ $overrideRemaining }} min</strong></span>
        </div>
        <form method="POST" action="{{ route('manager.override.revoke') }}" class="inline">
            @csrf
            <button type="submit" class="bg-amber-950 text-amber-100 hover:bg-black px-2.5 py-1 rounded text-[11px] font-bold transition">
                Lock &amp; Exit POS Admin
            </button>
        </form>
    </div>
@endif

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-xs sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-3">
            
            <!-- Left Brand & Navigation Menu -->
            <div class="flex items-center gap-2 xl:gap-4 min-w-0">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ $isCashier && !$isManagerOverridden ? route('pos.index') : route('dashboard') }}" class="flex items-center gap-2 text-indigo-700 font-black text-lg xl:text-xl tracking-tight shrink-0 whitespace-nowrap group">
                        <span class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-indigo-800 text-white rounded-lg flex items-center justify-center font-black text-base shadow-xs group-hover:scale-105 transition-transform">e</span>
                        <span class="font-black text-gray-900 tracking-tight">erp<span class="text-indigo-600">GEN</span></span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden lg:flex lg:items-center space-x-0.5 xl:space-x-1 text-xs xl:text-sm font-medium">
                    <!-- POS Quick Button (Always prominent) -->
                    <a href="{{ route('pos.index') }}" class="px-2.5 xl:px-3 py-1.5 rounded-lg transition bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1 shadow-xs shrink-0 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        POS Terminal
                    </a>

                    @if (!$isCashier || $isManagerOverridden)
                        <a href="{{ route('dashboard') }}" class="px-2 xl:px-2.5 py-1.5 rounded-lg transition shrink-0 whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            Dashboard
                        </a>

                        <!-- Inventory Dropdown -->
                        <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="px-2 xl:px-2.5 py-1.5 rounded-lg transition flex items-center gap-1 shrink-0 whitespace-nowrap {{ request()->routeIs(['products.*', 'categories.*', 'brands.*', 'units.*', 'inventory.*', 'warehouses.*', 'transfers.*']) ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <span>Inventory</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" style="display: none;" class="absolute left-0 mt-1.5 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50">
                                <a href="{{ route('products.index') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Products Catalog</a>
                                <a href="{{ route('inventory.index') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Stock Movements</a>
                                <a href="{{ route('inventory.alerts') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Low Stock & Expiry Alerts</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('warehouses.index') }}" class="block px-4 py-1.5 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Warehouse Stocks</a>
                                <a href="{{ route('transfers.index') }}" class="block px-4 py-1.5 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Inter-Branch Transfers</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('categories.index') }}" class="block px-4 py-1.5 text-xs text-gray-600 hover:bg-indigo-50 hover:text-indigo-700">Categories</a>
                                <a href="{{ route('brands.index') }}" class="block px-4 py-1.5 text-xs text-gray-600 hover:bg-indigo-50 hover:text-indigo-700">Brands</a>
                                <a href="{{ route('units.index') }}" class="block px-4 py-1.5 text-xs text-gray-600 hover:bg-indigo-50 hover:text-indigo-700">Units</a>
                            </div>
                        </div>

                        <!-- Sales & Orders -->
                        <a href="{{ route('sales.index') }}" class="px-2 xl:px-2.5 py-1.5 rounded-lg transition shrink-0 whitespace-nowrap {{ request()->routeIs('sales.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            Sales
                        </a>

                        <!-- Purchases -->
                        <a href="{{ route('purchases.index') }}" class="px-2 xl:px-2.5 py-1.5 rounded-lg transition shrink-0 whitespace-nowrap {{ request()->routeIs('purchases.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            Purchases
                        </a>

                        <!-- CRM / People Dropdown -->
                        <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="px-2 xl:px-2.5 py-1.5 rounded-lg transition flex items-center gap-1 shrink-0 whitespace-nowrap {{ request()->routeIs(['members.*', 'customers.*', 'suppliers.*']) ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <span>CRM</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" style="display: none;" class="absolute left-0 mt-1.5 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50">
                                <a href="{{ route('members.index') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Membership Cards</a>
                                <a href="{{ route('customers.index') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Customers</a>
                                <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Suppliers</a>
                            </div>
                        </div>

                        <!-- Financials Dropdown -->
                        <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="px-2 xl:px-2.5 py-1.5 rounded-lg transition flex items-center gap-1 shrink-0 whitespace-nowrap {{ request()->routeIs(['expenses.*', 'accounts.*', 'shifts.*', 'promotions.*']) ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <span>Finance</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" style="display: none;" class="absolute left-0 mt-1.5 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50">
                                <a href="{{ route('expenses.index') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Expenses</a>
                                <a href="{{ route('accounts.ledger') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">General Ledger</a>
                                <a href="{{ route('shifts.index') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Cashier Shifts</a>
                                <a href="{{ route('promotions.index') }}" class="block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Promotions & Discounts</a>
                            </div>
                        </div>

                        <!-- Reports -->
                        <a href="{{ route('reports.index') }}" class="px-2 xl:px-2.5 py-1.5 rounded-lg transition shrink-0 whitespace-nowrap {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            Reports
                        </a>
                    @else
                        <!-- Cashier Mode Quick Actions -->
                        <a href="{{ route('shifts.index') }}" class="px-2.5 py-1.5 rounded-lg transition text-gray-600 hover:text-gray-900 hover:bg-gray-50 text-xs font-semibold flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            My Shifts
                        </a>

                        <a href="{{ route('manager.override.form') }}" class="px-2.5 py-1.5 rounded-lg transition bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold flex items-center gap-1 shrink-0 shadow-2xs" title="Unlock administrative features with Manager Password">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Manager Unlock
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right: Branch Switcher & User Profile -->
            <div class="hidden lg:flex lg:items-center gap-2 xl:gap-3 shrink-0">
                <!-- Branch Indicator / Switcher -->
                @if ($isCashier && !$isManagerOverridden)
                    <!-- Locked Branch Badge for Cashiers -->
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 border border-emerald-200 text-xs font-bold rounded-xl text-emerald-800 bg-emerald-50 shadow-xs max-w-[210px]" title="Assigned Branch: Cashier is restricted to this branch">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="truncate text-[11px] xl:text-xs">{{ $activeBranchObj ? $activeBranchObj->name : (Auth::user()->branch?->name ?? 'Assigned Outlet') }}</span>
                        <span class="px-1 py-0.2 bg-emerald-200 text-emerald-900 rounded text-[9px] font-black uppercase tracking-wider">Locked</span>
                    </div>
                @else
                    <!-- Branch Switcher Dropdown for Admins / Overridden Sessions -->
                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 border border-indigo-200 text-xs font-bold rounded-xl text-indigo-700 bg-indigo-50/70 hover:bg-indigo-100/70 transition shadow-xs max-w-[170px] xl:max-w-[210px]">
                            <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span class="truncate text-[11px] xl:text-xs">{{ $activeBranchObj ? $activeBranchObj->name : 'All Outlets (HQ)' }}</span>
                            <svg class="w-3 h-3 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" style="display: none;" class="absolute right-0 mt-1.5 w-60 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50">
                            <div class="px-3 py-1 text-[10px] font-black uppercase tracking-wider text-gray-400">Switch Store Context</div>
                            <form action="{{ route('branches.switch') }}" method="POST">
                                @csrf
                                <input type="hidden" name="branch_id" value="all">
                                <button type="submit" class="w-full text-left px-3 py-1.5 text-xs font-semibold hover:bg-indigo-50 hover:text-indigo-700 flex items-center justify-between {{ !$activeBranchId ? 'text-indigo-700 font-bold bg-indigo-50/50' : 'text-gray-700' }}">
                                    <span>All Outlets (Consolidated)</span>
                                    @if(!$activeBranchId) <span class="text-indigo-600 font-bold">✓</span> @endif
                                </button>
                            </form>
                            <div class="border-t border-gray-100 my-1"></div>
                            @foreach ($allBranches as $br)
                                <form action="{{ route('branches.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="{{ $br->id }}">
                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-xs font-semibold hover:bg-indigo-50 hover:text-indigo-700 flex items-center justify-between {{ $activeBranchId == $br->id ? 'text-indigo-700 font-bold bg-indigo-50/50' : 'text-gray-700' }}">
                                        <span class="truncate">{{ $br->name }}</span>
                                        @if($activeBranchId == $br->id) <span class="text-indigo-600 font-bold">✓</span> @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- User Profile Dropdown -->
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-2.5 py-1.5 border border-gray-200 text-xs font-semibold rounded-xl text-gray-700 bg-gray-50/80 hover:bg-gray-100 hover:border-gray-300 focus:outline-none transition shrink-0 whitespace-nowrap shadow-xs">
                            <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-black flex items-center justify-center text-[11px] shadow-xs shrink-0">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex flex-col text-left leading-tight shrink-0">
                                <span class="text-[11px] xl:text-xs font-black text-gray-900 whitespace-nowrap max-w-[90px] xl:max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <span class="text-[9px] text-indigo-600 font-bold tracking-wide uppercase whitespace-nowrap">{{ Auth::user()->role?->name ?? 'Staff' }}</span>
                            </div>
                            <svg class="fill-current h-3 w-3 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit', ['tab' => 'dashboard'])">
                            {{ __('My Profile & Dashboard') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit', ['tab' => 'history'])">
                            {{ __('My Sales History') }}
                        </x-dropdown-link>

                        @if (Auth::user()->isSuperAdmin() || Auth::user()->hasRole('admin') || $isManagerOverridden)
                            <x-dropdown-link :href="route('branches.index')">
                                {{ __('Branches & Outlets') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('users.index')">
                                {{ __('Staff & Roles') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('audit_logs.index')">
                                {{ __('Audit Logs') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('settings.index')">
                                {{ __('Settings') }}
                            </x-dropdown-link>
                        @endif

                        <div class="border-t border-gray-100 my-1"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="-me-2 flex items-center lg:hidden shrink-0">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden border-t border-gray-200 bg-white px-4 pt-2 pb-4 space-y-1">
        <!-- Mobile Branch Context Indicator -->
        <div class="py-2 border-b border-gray-100 mb-2">
            <span class="text-[10px] font-bold uppercase text-gray-400">Current Outlet:</span>
            <div class="mt-1 font-bold text-sm text-indigo-700 flex items-center justify-between">
                <span>{{ $activeBranchObj ? $activeBranchObj->name : (Auth::user()->branch?->name ?? 'All Outlets') }}</span>
                @if ($isCashier && !$isManagerOverridden)
                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-800 rounded text-[10px] font-black">Locked</span>
                @endif
            </div>
        </div>

        <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded-md text-sm font-bold text-emerald-600 hover:bg-emerald-50">POS Terminal</a>
        <a href="{{ route('shifts.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cashier Shifts</a>
        <a href="{{ route('profile.edit', ['tab' => 'dashboard']) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-indigo-700 hover:bg-indigo-50">My Profile &amp; Dashboard</a>
        <a href="{{ route('profile.edit', ['tab' => 'history']) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-indigo-700 hover:bg-indigo-50">My Sales History</a>

        @if (!$isCashier || $isManagerOverridden)
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Dashboard</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Products Catalog</a>
            <a href="{{ route('inventory.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Stock Movements</a>
            <a href="{{ route('inventory.alerts') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Stock Alerts</a>
            <a href="{{ route('warehouses.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Warehouses</a>
            <a href="{{ route('transfers.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Stock Transfers</a>
            <a href="{{ route('sales.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Sales & Orders</a>
            <a href="{{ route('purchases.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Purchases</a>
            <a href="{{ route('members.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Memberships</a>
            <a href="{{ route('customers.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Customers</a>
            <a href="{{ route('suppliers.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Suppliers</a>
            <a href="{{ route('expenses.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Expenses</a>
            <a href="{{ route('accounts.ledger') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">General Ledger</a>
            <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Reports</a>
            <a href="{{ route('branches.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Branches & Outlets</a>
            <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Staff & Roles</a>
            <a href="{{ route('settings.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Settings</a>
        @else
            <a href="{{ route('manager.override.form') }}" class="block px-3 py-2 rounded-md text-sm font-bold text-amber-700 hover:bg-amber-50">🔑 Manager Password Unlock</a>
        @endif
    </div>
</nav>

