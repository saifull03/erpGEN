<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Membership Management</h2>
                <p class="text-sm text-gray-500 mt-1">Register new members, manage loyalty tiers, and track customer reward points.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-100 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    Go to POS
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ 
        editModalOpen: false,
        editingMember: null,
        mode: '{{ $selectedCustomerId ? 'existing' : 'new' }}',
        selectedCustomer: '{{ $selectedCustomerId ?? '' }}',
        customerMap: {
            @foreach ($customers as $c)
                '{{ $c->id }}': {
                    name: '{{ addslashes($c->name) }}',
                    phone: '{{ addslashes($c->phone ?? '') }}',
                    email: '{{ addslashes($c->email ?? '') }}',
                    address: '{{ addslashes($c->address ?? '') }}',
                    dob: '{{ $c->date_of_birth ?? '' }}'
                },
            @endforeach
        },
        initEdit(member) {
            this.editingMember = member;
            this.editModalOpen = true;
        },
        onCustomerChange() {
            if (this.selectedCustomer && this.customerMap[this.selectedCustomer]) {
                let data = this.customerMap[this.selectedCustomer];
                document.getElementById('reg_name').value = data.name;
                document.getElementById('reg_phone').value = data.phone;
                document.getElementById('reg_email').value = data.email;
                document.getElementById('reg_address').value = data.address;
                if (data.dob) document.getElementById('reg_dob').value = data.dob;
            }
        },
        setOneYearExpiry() {
            let today = new Date();
            today.setFullYear(today.getFullYear() + 1);
            let y = today.getFullYear();
            let m = String(today.getMonth() + 1).padStart(2, '0');
            let d = String(today.getDate()).padStart(2, '0');
            document.getElementById('reg_expiry_date').value = `${y}-${m}-${d}`;
        }
    }">

        <!-- Flash messages -->
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <h3 class="text-red-800 font-semibold text-sm">Please correct the errors below:</h3>
                </div>
                <ul class="list-disc pl-8 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Members</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\Member::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active Members</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ \App\Models\Member::where('status', 'active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Points Issued</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format(\App\Models\Member::sum('points')) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Available Tiers</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $membershipTypes->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                </div>
            </div>
        </div>

        <!-- Registration Section Card -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-700 via-indigo-600 to-indigo-800 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/10 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Register New Membership</h3>
                        <p class="text-xs text-indigo-100">Enroll a new customer or attach membership benefits to an existing customer.</p>
                    </div>
                </div>

                <!-- Toggle Mode: New Customer vs Link Existing -->
                <div class="flex items-center bg-indigo-900/50 p-1 rounded-lg border border-indigo-500/30 text-xs font-medium">
                    <button type="button" @click="mode = 'new'; selectedCustomer = ''" :class="mode === 'new' ? 'bg-white text-indigo-700 shadow-sm' : 'text-indigo-200 hover:text-white'" class="px-3 py-1.5 rounded-md transition font-semibold">
                        + New Customer
                    </button>
                    <button type="button" @click="mode = 'existing'" :class="mode === 'existing' ? 'bg-white text-indigo-700 shadow-sm' : 'text-indigo-200 hover:text-white'" class="px-3 py-1.5 rounded-md transition font-semibold">
                        Link Existing Customer
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('members.store') }}" class="p-6">
                @csrf

                <!-- Existing Customer Select Box (shown if mode === 'existing') -->
                <div x-show="mode === 'existing'" class="mb-6 p-4 bg-indigo-50/60 border border-indigo-100 rounded-lg transition">
                    <label for="customer_select" class="block text-sm font-semibold text-indigo-900 mb-1">
                        Select Existing Customer <span class="text-red-500">*</span>
                    </label>
                    <select id="customer_select" name="customer_id" x-model="selectedCustomer" @change="onCustomerChange()" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Choose a customer from database --</option>
                        @foreach ($customers as $cust)
                            <option value="{{ $cust->id }}" {{ ($selectedCustomerId == $cust->id || old('customer_id') == $cust->id) ? 'selected' : '' }}>
                                {{ $cust->name }} (ID: {{ $cust->customer_id }}) - {{ $cust->phone ?? 'No phone' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-indigo-600 mt-1.5">Selecting a customer will automatically autofill and link their profile with the membership card.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Member Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1" for="reg_name">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input id="reg_name" name="name" type="text" value="{{ old('name') }}" placeholder="e.g. John Doe" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1" for="reg_phone">
                            Phone Number
                        </label>
                        <input id="reg_phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="e.g. +8801700000000" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1" for="reg_email">
                            Email Address
                        </label>
                        <input id="reg_email" name="email" type="email" value="{{ old('email') }}" placeholder="e.g. john@example.com" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Membership Tier / Type -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1" for="reg_membership_type">
                            Membership Tier <span class="text-red-500">*</span>
                        </label>
                        <select id="reg_membership_type" name="membership_type_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            @foreach ($membershipTypes as $tier)
                                <option value="{{ $tier->id }}" {{ old('membership_type_id') == $tier->id ? 'selected' : '' }}>
                                    {{ $tier->name }} ({{ $tier->discount_percentage }}% Disc, {{ $tier->reward_points }} pts)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Membership ID (Auto or Custom) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider" for="reg_membership_id">
                                Membership ID
                            </label>
                            <span class="text-[11px] text-gray-400 font-normal">Leave blank to auto-generate</span>
                        </div>
                        <input id="reg_membership_id" name="membership_id" type="text" value="{{ old('membership_id') }}" placeholder="Auto generated (e.g. MEM-0001)" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono">
                    </div>

                    <!-- Member Number (Card / Barcode Number) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider" for="reg_member_number">
                                Member Number / Card #
                            </label>
                            <span class="text-[11px] text-gray-400 font-normal">Used at POS Checkout</span>
                        </div>
                        <input id="reg_member_number" name="member_number" type="text" value="{{ old('member_number') }}" placeholder="Auto generated (e.g. M-1001)" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono">
                    </div>

                    <!-- Join Date -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1" for="reg_join_date">
                            Join Date
                        </label>
                        <input id="reg_join_date" name="join_date" type="date" value="{{ old('join_date', date('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider" for="reg_expiry_date">
                                Expiry Date <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <button type="button" @click="setOneYearExpiry()" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium">
                                +1 Year
                            </button>
                        </div>
                        <input id="reg_expiry_date" name="expiry_date" type="date" value="{{ old('expiry_date') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1" for="reg_dob">
                            Date of Birth <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <input id="reg_dob" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1" for="reg_address">
                            Address
                        </label>
                        <input id="reg_address" name="address" type="text" value="{{ old('address') }}" placeholder="Street, City, Country" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="reset" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Reset Form
                    </button>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Complete Membership Registration
                    </button>
                </div>
            </form>
        </div>

        <!-- Filter & Members Directory -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <!-- Search & Filters Bar -->
            <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" action="{{ route('members.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Search</label>
                        <div class="relative">
                            <input name="search" value="{{ request('search') }}" placeholder="Name, Phone, Member #, ID..." class="w-full pl-9 text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tier</label>
                        <select name="membership_type_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Tiers</option>
                            @foreach ($membershipTypes as $tier)
                                <option value="{{ $tier->id }}" {{ request('membership_type_id') == $tier->id ? 'selected' : '' }}>{{ $tier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-semibold transition">
                            Filter
                        </button>
                        @if (request()->hasAny(['search', 'membership_type_id', 'status']))
                            <a href="{{ route('members.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Members List Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Member Info</th>
                            <th class="px-5 py-3.5 text-left">Membership & Tier</th>
                            <th class="px-5 py-3.5 text-left">Contact</th>
                            <th class="px-5 py-3.5 text-center">Points</th>
                            <th class="px-5 py-3.5 text-left">Joined / Expiry</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($members as $member)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                                    <div class="text-xs text-gray-500 font-mono">Card: <span class="font-bold text-indigo-700">{{ $member->member_number }}</span></div>
                                    @if ($member->customer)
                                        <div class="text-[11px] text-gray-400">Cust ID: {{ $member->customer->customer_id }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-mono text-xs font-semibold text-gray-800">{{ $member->membership_id }}</div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($member->membershipType?->name === 'Platinum') bg-purple-100 text-purple-800
                                        @elseif($member->membershipType?->name === 'Gold') bg-amber-100 text-amber-800
                                        @elseif($member->membershipType?->name === 'Silver') bg-slate-100 text-slate-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $member->membershipType?->name ?? 'Standard' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-gray-900">{{ $member->phone ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->email ?? '—' }}</div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 font-bold text-xs border border-amber-200">
                                        ★ {{ number_format($member->points) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">
                                    <div>Join: {{ \Carbon\Carbon::parse($member->join_date)->format('M d, Y') }}</div>
                                    @if ($member->expiry_date)
                                        <div class="text-gray-400">Exp: {{ \Carbon\Carbon::parse($member->expiry_date)->format('M d, Y') }}</div>
                                    @else
                                        <div class="text-emerald-600">Lifetime / No Expiry</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($member->status === 'active') bg-emerald-100 text-emerald-800
                                        @elseif($member->status === 'suspended') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($member->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" @click="initEdit({{ json_encode($member) }})" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-md transition" title="Edit Member">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Are you sure you want to delete member {{ $member->name }} ({{ $member->member_number }})?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition" title="Delete Member">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    <p class="text-base font-semibold text-gray-700">No members found</p>
                                    <p class="text-xs text-gray-400 mt-1">Register a member above to get started with the loyalty program.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($members->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $members->links() }}
                </div>
            @endif
        </div>

        <!-- Edit Member Modal -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" @click="editModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="editModalOpen" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <form x-bind:action="'/members/' + (editingMember ? editingMember.id : '')" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-6 pt-5 pb-4 sm:p-6">
                            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Edit Member Details</h3>
                                <button type="button" @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Member Name</label>
                                    <input name="name" x-bind:value="editingMember ? editingMember.name : ''" class="w-full rounded-lg border-gray-300 text-sm" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Phone Number</label>
                                    <input name="phone" x-bind:value="editingMember ? editingMember.phone : ''" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email Address</label>
                                    <input name="email" type="email" x-bind:value="editingMember ? editingMember.email : ''" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Membership Tier</label>
                                    <select name="membership_type_id" x-bind:value="editingMember ? editingMember.membership_type_id : ''" class="w-full rounded-lg border-gray-300 text-sm" required>
                                        @foreach ($membershipTypes as $tier)
                                            <option value="{{ $tier->id }}">{{ $tier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Membership ID</label>
                                    <input name="membership_id" x-bind:value="editingMember ? editingMember.membership_id : ''" class="w-full rounded-lg border-gray-300 text-sm font-mono" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Member Card #</label>
                                    <input name="member_number" x-bind:value="editingMember ? editingMember.member_number : ''" class="w-full rounded-lg border-gray-300 text-sm font-mono" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                                    <select name="status" x-bind:value="editingMember ? editingMember.status : 'active'" class="w-full rounded-lg border-gray-300 text-sm" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Reward Points</label>
                                    <input name="points" type="number" min="0" x-bind:value="editingMember ? editingMember.points : 0" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Join Date</label>
                                    <input name="join_date" type="date" x-bind:value="editingMember ? editingMember.join_date : ''" class="w-full rounded-lg border-gray-300 text-sm" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Expiry Date</label>
                                    <input name="expiry_date" type="date" x-bind:value="editingMember ? editingMember.expiry_date : ''" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Address</label>
                                    <input name="address" x-bind:value="editingMember ? editingMember.address : ''" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:text-sm">
                                Save Changes
                            </button>
                            <button type="button" @click="editModalOpen = false" class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
