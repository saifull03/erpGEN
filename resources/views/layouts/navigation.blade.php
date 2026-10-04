<nav x-data="{ open: false, catalogDropdown: false, financeDropdown: false, reportsDropdown: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-indigo-700 font-black text-xl tracking-tight">
                        <span class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-black text-lg shadow-sm">1</span>
                        <span>OneStop<span class="text-xs text-indigo-500 font-semibold ml-1">POS+ERP</span></span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden lg:flex lg:items-center lg:space-x-1 lg:ms-8 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Dashboard
                    </a>

                    <!-- POS Quick Button -->
                    <a href="{{ route('pos.index') }}" class="px-3 py-1.5 rounded-md transition bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        POS
                    </a>

                    <!-- Catalog Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md transition flex items-center gap-1 {{ request()->routeIs(['products.*', 'categories.*', 'brands.*', 'units.*', 'inventory.*']) ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <span>Inventory</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" style="display: none;" class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                            <a href="{{ route('products.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Products</a>
                            <a href="{{ route('inventory.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Stock Movements</a>
                            <a href="{{ route('inventory.alerts') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Stock & Expiry Alerts</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Categories</a>
                            <a href="{{ route('brands.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Brands</a>
                            <a href="{{ route('units.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Units</a>
                        </div>
                    </div>

                    <a href="{{ route('sales.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('sales.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Sales
                    </a>

                    <a href="{{ route('purchases.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('purchases.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Purchases
                    </a>

                    <a href="{{ route('members.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('members.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Members
                    </a>

                    <a href="{{ route('customers.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Customers
                    </a>

                    <a href="{{ route('suppliers.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('suppliers.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Suppliers
                    </a>

                    <!-- Financials Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="px-3 py-2 rounded-md transition flex items-center gap-1 {{ request()->routeIs(['expenses.*', 'accounts.*', 'shifts.*']) ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <span>Finance</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" style="display: none;" class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                            <a href="{{ route('expenses.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Expenses</a>
                            <a href="{{ route('accounts.ledger') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">General Ledger</a>
                            <a href="{{ route('shifts.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Cashier Shifts</a>
                            <a href="{{ route('promotions.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Promotions</a>
                        </div>
                    </div>

                    <a href="{{ route('reports.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Reports
                    </a>
                </div>
            </div>

            <!-- Settings / User Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-semibold rounded-lg text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none transition">
                            <div class="flex flex-col text-right mr-2">
                                <span class="text-xs font-bold text-gray-900">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-indigo-600 font-medium">{{ Auth::user()->role?->name ?? 'Staff' }}</span>
                            </div>
                            <svg class="fill-current h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('My Profile') }}
                        </x-dropdown-link>

                        @if (Auth::user()->isSuperAdmin() || Auth::user()->hasRole('admin'))
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

                        <div class="border-t border-gray-100"></div>

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
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
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
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Dashboard</a>
        <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded-md text-sm font-bold text-emerald-600 hover:bg-emerald-50">POS Terminal</a>
        <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Products & Inventory</a>
        <a href="{{ route('sales.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Sales & Returns</a>
        <a href="{{ route('purchases.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Purchasing</a>
        <a href="{{ route('members.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Memberships</a>
        <a href="{{ route('customers.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Customers</a>
        <a href="{{ route('suppliers.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Suppliers</a>
        <a href="{{ route('expenses.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Expenses</a>
        <a href="{{ route('accounts.ledger') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">General Ledger</a>
        <a href="{{ route('shifts.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cashier Shifts</a>
        <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Reports</a>
        <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Staff & Roles</a>
        <a href="{{ route('settings.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Settings</a>
    </div>
</nav>
