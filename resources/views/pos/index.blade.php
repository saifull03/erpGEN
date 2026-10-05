<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <h2 class="font-black text-2xl text-gray-900 tracking-tight flex items-center gap-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                    POS Terminal
                </h2>
                @if ($activeShift)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Shift #{{ $activeShift->shift_number }} (Open)
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        No Active Shift
                    </span>
                @endif
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2">
                @if ($lastSale)
                    <a href="{{ route('sales.thermal', $lastSale) }}" target="_blank" class="px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5" title="Print Last Thermal Receipt (F11)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        <span>🖨️ Last Receipt</span>
                    </a>
                @endif

                @if ($activeShift)
                    <button type="button" @click="$dispatch('open-close-shift-modal')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition border border-gray-200">
                        Close Shift
                    </button>
                @else
                    <button type="button" @click="$dispatch('open-start-shift-modal')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        Open Shift
                    </button>
                @endif

                <button type="button" @click="$dispatch('open-held-sales-modal')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-semibold rounded-lg transition flex items-center gap-1">
                    <span>Held Carts</span>
                    @if ($heldSales->count() > 0)
                        <span class="px-1.5 py-0.2 bg-amber-600 text-white rounded-full text-[10px]">{{ $heldSales->count() }}</span>
                    @endif
                </button>

                <!-- Manager Password Authorization Modal Trigger -->
                <button type="button" @click="$dispatch('open-manager-modal')" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5" title="Branch Manager Authorization">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Manager Unlock</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{
        // POS State
        searchQuery: '',
        searchResults: [],
        searchLoading: false,
        memberSearchTerm: '{{ $activeMember?->member_number ?? '' }}',
        memberSearchResults: [],
        memberSearchLoading: false,
        activeMember: {{ json_encode($activeMember) }},
        invoiceDiscount: 0,
        pointsConversionRate: {{ (float) (\App\Models\Setting::where('key', 'loyalty_discount_per_hundred_points')->value('value') ?? 150) }},
        redeemPoints: 0,
        taxRate: {{ \App\Models\Setting::where('key', 'tax_rate')->value('value') ?? 0 }},
        paymentModalOpen: false,
        splitCash: {{ $subtotal }},
        splitCard: 0,
        splitBkash: 0,
        splitNagad: 0,
        memberLookupLoading: false,
        memberModalOpen: false,
        heldModalOpen: false,
        shiftModalOpen: false,
        closeShiftModalOpen: false,
        shortcutsModalOpen: false,
        managerModalOpen: false,
        managerEmail: '',
        managerPassword: '',
        managerAction: 'backoffice',
        managerAuthError: '',
        managerAuthSuccess: '',
        managerAuthLoading: false,

        // Quick Member Registration State
        newMemberName: '',
        newMemberPhone: '',
        newMemberEmail: '',
        newMemberTier: '{{ $membershipTypes->first()?->id ?? 1 }}',
        regLoading: false,
        regError: '',
        regSuccess: '',

        // Calculated values
        rawSubtotal: {{ $subtotal }},
        get memberDiscount() {
            if (!this.activeMember || !this.activeMember.membership_type) return 0;
            let pct = parseFloat(this.activeMember.membership_type.discount_percentage || 0);
            return pct > 0 ? (this.rawSubtotal * pct) / 100 : 0;
        },
        get pointsDiscount() {
            if (!this.activeMember) return 0;
            let pts = parseInt(this.redeemPoints) || 0;
            if (pts <= 0) return 0;
            let maxPts = parseInt(this.activeMember.points) || 0;
            let usablePts = Math.min(pts, maxPts);
            let rate = parseFloat(this.pointsConversionRate) / 100;
            let disc = usablePts * rate;
            let remainingSubtotal = Math.max(0, this.rawSubtotal - this.memberDiscount);
            return Math.min(remainingSubtotal, Math.round(disc * 100) / 100);
        },
        get totalDiscount() {
            let invDisc = parseFloat(this.invoiceDiscount) || 0;
            return Math.min(this.rawSubtotal, this.memberDiscount + invDisc + this.pointsDiscount);
        },
        setRedeemPoints(pts) {
            if (!this.activeMember) return;
            let maxAvailable = parseInt(this.activeMember.points) || 0;
            let targetPts = Math.min(Math.max(0, parseInt(pts) || 0), maxAvailable);
            this.redeemPoints = targetPts;
        },
        get taxAmount() {
            let taxable = Math.max(0, this.rawSubtotal - this.totalDiscount);
            return (taxable * this.taxRate) / 100;
        },
        get grandTotal() {
            return Math.max(0, this.rawSubtotal - this.totalDiscount + this.taxAmount);
        },
        tenderedCash: '',
        get quickTenderedCash() {
            if (this.tenderedCash === '' || this.tenderedCash === null) return this.grandTotal;
            return Math.max(0, parseFloat(this.tenderedCash) || 0);
        },
        get quickChangeAmount() {
            return Math.max(0, this.quickTenderedCash - this.grandTotal);
        },
        get quickDueAmount() {
            return Math.max(0, this.grandTotal - this.quickTenderedCash);
        },
        get totalPaidAmount() {
            return (parseFloat(this.splitCash) || 0) + 
                   (parseFloat(this.splitCard) || 0) + 
                   (parseFloat(this.splitBkash) || 0) + 
                   (parseFloat(this.splitNagad) || 0);
        },
        get changeAmount() {
            return Math.max(0, this.totalPaidAmount - this.grandTotal);
        },
        get dueAmount() {
            return Math.max(0, this.grandTotal - this.totalPaidAmount);
        },

        // Methods
        async searchProductsLive() {
            if (this.searchQuery.trim().length < 1) {
                this.searchResults = [];
                return;
            }
            this.searchLoading = true;
            try {
                let res = await fetch(`{{ route('pos.search_products') }}?q=${encodeURIComponent(this.searchQuery)}`);
                if (res.ok) {
                    this.searchResults = await res.json();
                }
            } catch (e) {} finally {
                this.searchLoading = false;
            }
        },

        async selectProduct(barcodeOrSku) {
            this.searchResults = [];
            this.searchQuery = '';
            document.getElementById('barcode_input').value = barcodeOrSku;
            document.getElementById('add_product_form').submit();
        },

        async searchMembersLive() {
            if (this.memberSearchTerm.trim().length < 1) {
                this.memberSearchResults = [];
                return;
            }
            this.memberSearchLoading = true;
            try {
                let res = await fetch(`{{ route('pos.search_members') }}?q=${encodeURIComponent(this.memberSearchTerm)}`);
                if (res.ok) {
                    this.memberSearchResults = await res.json();
                }
            } catch (e) {} finally {
                this.memberSearchLoading = false;
            }
        },

        selectMember(member) {
            this.activeMember = {
                id: member.id,
                name: member.name,
                member_number: member.member_number,
                phone: member.phone,
                points: member.points,
                membership_type: {
                    name: member.tier,
                    discount_percentage: member.discount_percentage
                }
            };
            this.redeemPoints = 0;
            this.memberSearchTerm = member.member_number;
            this.memberSearchResults = [];
            document.getElementById('member_number_input').value = member.member_number;
        },

        async lookupMemberDetails() {
            if (!this.memberSearchTerm.trim()) return;
            this.memberLookupLoading = true;
            try {
                let res = await fetch(`{{ route('pos.lookup_member') }}?term=${encodeURIComponent(this.memberSearchTerm)}`);
                let data = await res.json();
                if (res.ok && data.success) {
                    this.selectMember(data.member);
                } else if (this.memberSearchResults.length > 0) {
                    this.selectMember(this.memberSearchResults[0]);
                } else {
                    alert('Member not found for given number/phone.');
                }
            } catch (e) {
                alert('Error searching member.');
            } finally {
                this.memberLookupLoading = false;
                this.memberSearchResults = [];
            }
        },

        clearMember() {
            this.activeMember = null;
            this.redeemPoints = 0;
            this.memberSearchTerm = '';
            this.memberSearchResults = [];
            document.getElementById('member_number_input').value = '';
        },

        async registerMemberInline() {
            if (!this.newMemberName.trim()) {
                this.regError = 'Member name is required.';
                return;
            }
            this.regLoading = true;
            this.regError = '';
            try {
                let res = await fetch('{{ route('members.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        name: this.newMemberName,
                        phone: this.newMemberPhone,
                        email: this.newMemberEmail,
                        membership_type_id: this.newMemberTier
                    })
                });
                let data = await res.json();
                if (res.ok && data.success) {
                    this.activeMember = data.member;
                    this.redeemPoints = 0;
                    this.memberSearchTerm = data.member.member_number;
                    document.getElementById('member_number_input').value = data.member.member_number;
                    this.regSuccess = `Registered: ${data.member.name}`;
                    setTimeout(() => {
                        this.memberModalOpen = false;
                        this.newMemberName = '';
                        this.newMemberPhone = '';
                        this.newMemberEmail = '';
                        this.regSuccess = '';
                    }, 1000);
                } else {
                    this.regError = data.message || 'Validation error.';
                }
            } catch (e) {
                this.regError = 'Network error.';
            } finally {
                this.regLoading = false;
            }
        },

        submitQuickCashPay() {
            let paidVal = this.quickTenderedCash;
            let payments = [{ method: 'cash', amount: parseFloat(paidVal) }];
            document.getElementById('payments_json_input').value = JSON.stringify(payments);
            document.getElementById('inv_discount_input').value = this.invoiceDiscount;
            document.getElementById('points_redeemed_input').value = this.redeemPoints;
            document.getElementById('complete_sale_form').submit();
        },

        submitCompleteCheckout() {
            let payments = [];
            if (parseFloat(this.splitCash) > 0) payments.push({ method: 'cash', amount: parseFloat(this.splitCash) });
            if (parseFloat(this.splitCard) > 0) payments.push({ method: 'card', amount: parseFloat(this.splitCard) });
            if (parseFloat(this.splitBkash) > 0) payments.push({ method: 'bkash', amount: parseFloat(this.splitBkash) });
            if (parseFloat(this.splitNagad) > 0) payments.push({ method: 'nagad', amount: parseFloat(this.splitNagad) });

            if (payments.length === 0) {
                payments.push({ method: 'cash', amount: this.grandTotal });
            }

            document.getElementById('payments_json_input').value = JSON.stringify(payments);
            document.getElementById('inv_discount_input').value = this.invoiceDiscount;
            document.getElementById('points_redeemed_input').value = this.redeemPoints;
            document.getElementById('complete_sale_form').submit();
        },

        printLastReceipt() {
            @if ($lastSale)
                window.open('{{ route('sales.thermal', $lastSale) }}', '_blank', 'width=450,height=600');
            @else
                alert('No prior receipt found in this session.');
            @endif
        },

        async submitManagerAuth() {
            if (!this.managerPassword) {
                this.managerAuthError = 'Please enter the branch manager password.';
                return;
            }
            this.managerAuthLoading = true;
            this.managerAuthError = '';
            this.managerAuthSuccess = '';
            try {
                let res = await fetch('{{ route('pos.manager_authorize') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        password: this.managerPassword,
                        email: this.managerEmail,
                        action: this.managerAction
                    })
                });
                let data = await res.json();
                if (res.ok && data.success) {
                    this.managerAuthSuccess = data.message;
                    this.managerPassword = '';
                    setTimeout(() => {
                        this.managerModalOpen = false;
                        if (this.managerAction === 'backoffice') {
                            window.location.href = '{{ route('dashboard') }}';
                        }
                    }, 800);
                } else {
                    this.managerAuthError = data.message || 'Authorization failed. Invalid credentials.';
                }
            } catch (e) {
                this.managerAuthError = 'Network error during authorization.';
            } finally {
                this.managerAuthLoading = false;
            }
        }
    }"
    @open-start-shift-modal.window="shiftModalOpen = true"
    @open-close-shift-modal.window="closeShiftModalOpen = true"
    @open-held-sales-modal.window="heldModalOpen = true"
    @open-manager-modal.window="managerModalOpen = true; managerAuthError = ''; managerAuthSuccess = ''"
    @keydown.f2.window.prevent="document.getElementById('barcode_input').focus()"
    @keydown.f4.window.prevent="document.getElementById('member_search_input').focus()"
    @keydown.f6.window.prevent="document.getElementById('invoice_discount_box').focus()"
    @keydown.f8.window.prevent="if ({{ count($cart) }} > 0) { splitCash = (tenderedCash !== '' ? tenderedCash : grandTotal.toFixed(2)); paymentModalOpen = true; }"
    @keydown.f9.window.prevent="document.getElementById('hold_form').submit()"
    @keydown.f10.window.prevent="if ({{ count($cart) }} > 0) submitQuickCashPay()"
    @keydown.f11.window.prevent="printLastReceipt()"
    @keydown.escape.window="paymentModalOpen = false; memberModalOpen = false; heldModalOpen = false; shiftModalOpen = false; closeShiftModalOpen = false; shortcutsModalOpen = false; managerModalOpen = false"
    >

        <!-- Flash messages -->
        @if (session('success'))
            <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-2xs flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-xs">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    @if ($lastSale)
                        <button type="button" @click="printLastReceipt()" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-lg shadow-2xs transition inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            Print Receipt (F11)
                        </button>
                        <a href="{{ route('sales.show', $lastSale) }}" class="px-3 py-1.5 bg-white border border-emerald-300 hover:bg-emerald-100 text-emerald-900 text-xs font-bold rounded-lg shadow-2xs transition inline-flex items-center gap-1">
                            View Invoice
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-3.5 rounded-r-lg text-red-800 text-sm shadow-sm">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- POS Workspace Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- LEFT 8 COLS: Scanner, Live Search, & Cart Items -->
            <div class="lg:col-span-8 flex flex-col gap-4">
                
                <!-- Barcode Scanner & Live Autocomplete Box -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <form id="add_product_form" method="POST" action="{{ route('pos.store') }}" class="flex gap-3">
                        @csrf
                        <div class="relative flex-1">
                            <input id="barcode_input" name="barcode" x-model="searchQuery" @input.debounce.250ms="searchProductsLive()" placeholder="Scan Barcode or Search Product Name / SKU (Press F2)..." class="w-full pl-10 pr-4 py-2.5 border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required autofocus autocomplete="off">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        </div>
                        <input name="quantity" type="number" min="1" value="1" class="w-20 py-2.5 border-gray-300 rounded-lg text-sm text-center font-bold">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Add
                        </button>
                    </form>

                    <!-- Live search dropdown suggestions -->
                    <div x-show="searchResults.length > 0" style="display: none;" class="mt-2 bg-white rounded-lg border border-gray-200 shadow-lg divide-y divide-gray-100 max-h-56 overflow-y-auto">
                        <template x-for="item in searchResults" :key="item.id">
                            <div @click="selectProduct(item.barcode || item.sku)" class="px-4 py-2.5 hover:bg-indigo-50 cursor-pointer flex items-center justify-between transition">
                                <div>
                                    <span class="text-sm font-bold text-gray-900" x-text="item.name"></span>
                                    <span class="text-xs text-gray-500 font-mono ml-2" x-text="'SKU: ' + item.sku"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-indigo-600" x-text="'৳ ' + parseFloat(item.selling_price).toFixed(2)"></span>
                                    <span class="text-xs text-gray-400 ml-2" x-text="'Stock: ' + item.current_stock"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Active Cart Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex-1 flex flex-col justify-between">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                                <tr>
                                    <th class="px-4 py-3 text-left">Item Name</th>
                                    <th class="px-4 py-3 text-center">Unit Price</th>
                                    <th class="px-4 py-3 text-center w-36">Quantity</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                    <th class="px-4 py-3 text-center w-12">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($products as $product)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900 text-sm">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-400 font-mono">{{ $product->barcode ?? $product->sku }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center font-semibold text-gray-700">
                                            ৳ {{ number_format($product->selling_price, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="inline-flex items-center rounded-lg border border-gray-200 p-0.5 bg-gray-50">
                                                <form method="POST" action="{{ route('pos.update', $product) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ max(1, $cart[$product->id] - 1) }}">
                                                    <button type="submit" class="w-7 h-7 rounded bg-white hover:bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-xs shadow-xs" @disabled($cart[$product->id] <= 1)>-</button>
                                                </form>
                                                <span class="w-10 text-center font-bold text-sm text-gray-900">{{ $cart[$product->id] }}</span>
                                                <form method="POST" action="{{ route('pos.update', $product) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $cart[$product->id] + 1 }}">
                                                    <button type="submit" class="w-7 h-7 rounded bg-white hover:bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-xs shadow-xs" @disabled($cart[$product->id] >= $product->current_stock)>+</button>
                                                </form>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right font-black text-gray-900">
                                            ৳ {{ number_format($product->selling_price * $cart[$product->id], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <form method="POST" action="{{ route('pos.remove', $product) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50 transition" title="Remove Item">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-16 text-center text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            <p class="text-base font-bold text-gray-600">Cart is empty</p>
                                            <p class="text-xs text-gray-400 mt-1">Scan a barcode or type SKU / Name in the search box above (F2).</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Cart Footer Shortcuts Bar -->
                    <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-200 text-xs font-semibold text-gray-500 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded text-[10px] text-gray-700 font-mono">F2</kbd> Search</span>
                            <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded text-[10px] text-gray-700 font-mono">F4</kbd> Member</span>
                            <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded text-[10px] text-gray-700 font-mono">F6</kbd> Discount</span>
                            <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded text-[10px] text-gray-700 font-mono">F8</kbd> Split Pay</span>
                            <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded text-[10px] text-gray-700 font-mono">F9</kbd> Hold</span>
                            <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded text-[10px] text-gray-700 font-mono">F10</kbd> Complete</span>
                            <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded text-[10px] text-gray-700 font-mono">F11</kbd> Reprint</span>
                        </div>
                        <div>
                            @if (!empty($cart))
                                <form method="POST" action="{{ route('pos.clear') }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold" onclick="return confirm('Clear active cart?');">Clear Cart</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT 4 COLS: Member Box, Bill Summary, & Checkout Actions -->
            <div class="lg:col-span-4 flex flex-col gap-4">
                
                <!-- Member / Customer Card Lookup (F4) -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1">
                            <span>Customer / Member (F4)</span>
                        </label>
                        <button type="button" @click="memberModalOpen = true" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                            + New Member
                        </button>
                    </div>

                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input id="member_search_input" x-model="memberSearchTerm" @input.debounce.200ms="searchMembersLive()" @keydown.enter.prevent="lookupMemberDetails()" placeholder="Enter Member Card #, Phone, Name..." class="w-full text-xs font-mono border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" autocomplete="off">
                                <template x-if="memberSearchLoading">
                                    <div class="absolute right-2.5 top-2">
                                        <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="lookupMemberDetails()" :disabled="memberLookupLoading" class="px-3 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition shadow-sm">
                                <span x-text="memberLookupLoading ? '...' : 'Find'"></span>
                            </button>
                        </div>

                        <!-- Live Autocomplete Jump Dropdown -->
                        <div x-show="memberSearchResults.length > 0" @click.outside="memberSearchResults = []" style="display: none;" class="absolute left-0 right-0 mt-1.5 bg-white rounded-lg border border-indigo-200 shadow-xl divide-y divide-gray-100 max-h-52 overflow-y-auto z-50">
                            <template x-for="item in memberSearchResults" :key="item.id">
                                <div @click="selectMember(item)" class="px-3 py-2.5 hover:bg-indigo-50 cursor-pointer flex items-center justify-between transition group">
                                    <div>
                                        <div class="text-xs font-bold text-gray-900 group-hover:text-indigo-700" x-text="item.name"></div>
                                        <div class="text-[11px] text-gray-500 font-mono">
                                            <span x-text="item.member_number"></span>
                                            <span class="mx-1">•</span>
                                            <span x-text="item.phone || 'No phone'"></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800" x-text="item.tier + ' (' + item.discount_percentage + '%)'"></span>
                                        <div class="text-[10px] text-amber-600 font-bold" x-text="'★ ' + item.points + ' pts'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Member info badge & Points Redemption -->
                    <template x-if="activeMember">
                        <div class="mt-3 space-y-2">
                            <div class="p-3 bg-indigo-50/80 border border-indigo-200 rounded-xl flex items-center justify-between shadow-2xs">
                                <div>
                                    <div class="text-xs font-bold text-indigo-900" x-text="activeMember.name"></div>
                                    <div class="text-[11px] text-indigo-700">
                                        <span class="font-semibold" x-text="activeMember.membership_type.name"></span> Tier 
                                        (<span x-text="activeMember.membership_type.discount_percentage + '% Tier Discount'"></span>)
                                    </div>
                                    <div class="text-[11px] text-amber-800 font-bold mt-0.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-1.5 py-0.2 bg-amber-100 text-amber-900 rounded text-[10px] font-black">★ <span x-text="activeMember.points"></span> pts</span>
                                        <span class="text-[10px] text-gray-500 font-medium">(Worth ৳ <span x-text="((activeMember.points / 100) * pointsConversionRate).toFixed(2)"></span> discount)</span>
                                    </div>
                                </div>
                                <button type="button" @click="clearMember()" class="text-xs text-red-500 hover:text-red-700 font-semibold px-2 py-1 hover:bg-red-50 rounded-lg transition">
                                    Remove
                                </button>
                            </div>

                            <!-- Loyalty Points Redemption Panel -->
                            <template x-if="activeMember.points > 0">
                                <div class="p-3 bg-gradient-to-r from-amber-50/90 to-emerald-50/90 border border-amber-200 rounded-xl space-y-2 shadow-2xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-black text-amber-900 uppercase tracking-wide flex items-center gap-1">
                                            <span>🎁 Redeem Loyalty Points</span>
                                        </span>
                                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100/80 border border-emerald-200 px-1.5 py-0.2 rounded-md">
                                            100 pts = ৳ <span x-text="pointsConversionRate"></span>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <input type="number" min="0" :max="activeMember.points" step="10" x-model.number="redeemPoints" placeholder="Points to redeem (e.g. 100)" class="w-full text-xs font-bold border-amber-300 rounded-lg shadow-2xs focus:ring-amber-500 focus:border-amber-500 bg-white">
                                        </div>
                                        <div class="text-xs font-black text-emerald-700 whitespace-nowrap bg-white px-2 py-1.5 rounded-lg border border-emerald-200 shadow-2xs">
                                            - ৳ <span x-text="pointsDiscount.toFixed(2)"></span>
                                        </div>
                                    </div>

                                    <!-- Quick Redemption Chips -->
                                    <div class="flex items-center gap-1 flex-wrap text-[10px]">
                                        <template x-if="activeMember.points >= 100">
                                            <button type="button" @click="setRedeemPoints(100)" class="px-2 py-0.5 rounded-md bg-white border border-amber-300 hover:bg-amber-100 text-amber-900 font-bold transition shadow-2xs">
                                                100 pts (-৳<span x-text="pointsConversionRate"></span>)
                                            </button>
                                        </template>
                                        <template x-if="activeMember.points >= 200">
                                            <button type="button" @click="setRedeemPoints(200)" class="px-2 py-0.5 rounded-md bg-white border border-amber-300 hover:bg-amber-100 text-amber-900 font-bold transition shadow-2xs">
                                                200 pts (-৳<span x-text="pointsConversionRate * 2"></span>)
                                            </button>
                                        </template>
                                        <button type="button" @click="setRedeemPoints(activeMember.points)" class="px-2 py-0.5 rounded-md bg-amber-600 hover:bg-amber-700 text-white font-bold transition shadow-2xs">
                                            Max (<span x-text="activeMember.points"></span> pts)
                                        </button>
                                        <template x-if="redeemPoints > 0">
                                            <button type="button" @click="redeemPoints = 0" class="px-1.5 py-0.5 rounded-md bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold transition" title="Clear Points">
                                                ✕
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Bill Calculation Summary Card -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider pb-3 border-b border-gray-100 flex items-center justify-between">
                            <span>Payment Summary</span>
                            <span class="text-xs font-bold text-indigo-600" x-text="'{{ count($cart) }} Items'"></span>
                        </h3>

                        <div class="space-y-3 mt-4 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-900">৳ <span x-text="rawSubtotal.toFixed(2)"></span></span>
                            </div>

                            <template x-if="memberDiscount > 0">
                                <div class="flex justify-between text-emerald-600 font-medium">
                                    <span>Membership Tier Discount</span>
                                    <span>- ৳ <span x-text="memberDiscount.toFixed(2)"></span></span>
                                </div>
                            </template>

                            <template x-if="pointsDiscount > 0">
                                <div class="flex justify-between text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-200">
                                    <span class="flex items-center gap-1">
                                        <span>🎁 Loyalty Points Discount (<span x-text="redeemPoints"></span> pts)</span>
                                    </span>
                                    <span>- ৳ <span x-text="pointsDiscount.toFixed(2)"></span></span>
                                </div>
                            </template>

                            <!-- Invoice Discount Input (F6) -->
                            <div class="flex items-center justify-between text-gray-600">
                                <label for="invoice_discount_box" class="text-xs font-semibold">Special Discount (F6)</label>
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-400">৳</span>
                                    <input id="invoice_discount_box" type="number" step="0.01" min="0" x-model="invoiceDiscount" class="w-24 text-right py-1 text-xs border-gray-300 rounded-md font-bold text-red-600">
                                </div>
                            </div>

                            <div class="flex justify-between text-gray-600">
                                <span>VAT / Tax (<span x-text="taxRate"></span>%)</span>
                                <span class="font-semibold text-gray-900">৳ <span x-text="taxAmount.toFixed(2)"></span></span>
                            </div>

                            <div class="pt-3 border-t border-gray-200 flex justify-between items-baseline">
                                <span class="text-base font-black text-gray-900">Grand Total</span>
                                <span class="text-2xl font-black text-indigo-700">৳ <span x-text="grandTotal.toFixed(2)"></span></span>
                            </div>

                            <!-- Live Cash Tendered / Customer Paid (৳) -->
                            <div class="pt-3 border-t border-gray-100 space-y-2">
                                <div class="flex items-center justify-between">
                                    <label for="cash_tendered_input" class="text-xs font-bold text-gray-800 uppercase tracking-wide">
                                        Customer Paid (৳)
                                    </label>
                                    <div class="flex items-center gap-1 text-[11px]">
                                        <button type="button" @click="tenderedCash = grandTotal.toFixed(2)" class="px-2 py-0.5 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">
                                            Exact
                                        </button>
                                        <button type="button" @click="tenderedCash = (500).toFixed(2)" class="px-2 py-0.5 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">
                                            500
                                        </button>
                                        <button type="button" @click="tenderedCash = (1000).toFixed(2)" class="px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold transition">
                                            1000
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-400 font-bold text-base">৳</span>
                                    <input id="cash_tendered_input" type="number" step="1" min="0" x-model="tenderedCash" placeholder="Enter Cash Given (e.g. 1000)" class="w-full pl-8 pr-4 py-2 text-lg font-black text-gray-900 border-2 border-indigo-200 rounded-xl focus:border-indigo-600 focus:ring-0 shadow-inner">
                                </div>

                                <!-- Instant Change Return Display (e.g. Paid 1000 on 200 bill -> Change 800) -->
                                <template x-if="quickTenderedCash > grandTotal">
                                    <div class="p-3 bg-emerald-600 text-white rounded-xl shadow-md flex items-center justify-between">
                                        <div>
                                            <div class="text-[10px] uppercase font-black tracking-widest text-emerald-100">Change to Return:</div>
                                            <div class="text-2xl font-black tracking-tight">৳ <span x-text="quickChangeAmount.toFixed(2)"></span></div>
                                        </div>
                                        <div class="p-2 bg-white/20 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="quickTenderedCash < grandTotal && quickTenderedCash > 0">
                                    <div class="p-3 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl flex items-center justify-between">
                                        <div>
                                            <div class="text-[10px] uppercase font-bold tracking-widest text-rose-600">Remaining Due:</div>
                                            <div class="text-xl font-black text-rose-700">৳ <span x-text="quickDueAmount.toFixed(2)"></span></div>
                                        </div>
                                        <span class="text-xs font-bold text-rose-600 px-2 py-1 bg-rose-100 rounded-md">Shortage</span>
                                    </div>
                                </template>

                                <template x-if="quickTenderedCash == grandTotal && grandTotal > 0">
                                    <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600 flex items-center justify-between font-medium">
                                        <span>Exact Payment Received</span>
                                        <span class="font-bold text-gray-800">Change: ৳ 0.00</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-2.5">
                        <!-- Complete Cash Checkout (F10) -->
                        <button type="button" @click="submitQuickCashPay()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl shadow-sm text-base flex items-center justify-center gap-2 transition" @disabled(empty($cart))>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Complete Cash Sale (F10)</span>
                        </button>

                        <!-- Multi-Payment / Split Option (F8) -->
                        <button type="button" @click="splitCash = (tenderedCash !== '' ? tenderedCash : grandTotal.toFixed(2)); splitCard = 0; splitBkash = 0; splitNagad = 0; paymentModalOpen = true" class="w-full py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg border border-indigo-200 text-sm flex items-center justify-center gap-1.5 transition" @disabled(empty($cart))>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            <span>Multi-Payment / Card / bKash (F8)</span>
                        </button>

                        <!-- Hold Sale (F9) -->
                        <form id="hold_form" method="POST" action="{{ route('pos.hold') }}" class="w-full">
                            @csrf
                            <input type="hidden" name="member_number" x-bind:value="activeMember ? activeMember.member_number : ''">
                            <button type="submit" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-xs transition" @disabled(empty($cart))>
                                Hold Sale (F9)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Checkout Form -->
        <form id="complete_sale_form" method="POST" action="{{ route('pos.complete') }}" style="display: none;">
            @csrf
            <input type="hidden" id="member_number_input" name="member_number" value="{{ $activeMember?->member_number ?? '' }}">
            <input type="hidden" id="inv_discount_input" name="invoice_discount" value="0">
            <input type="hidden" id="points_redeemed_input" name="points_redeemed" value="0">
            <input type="hidden" id="payments_json_input" name="payments" value="">
        </form>

        <!-- Split / Multi Payment Modal -->
        <div x-show="paymentModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div @click="paymentModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity"></div>
                <div class="inline-block bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full p-6 z-10">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                        <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Split & Multi Payment
                        </h3>
                        <button type="button" @click="paymentModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
                    </div>

                    <div class="space-y-3.5">
                        <div class="p-3 bg-indigo-50 rounded-xl flex justify-between items-center">
                            <span class="text-sm font-semibold text-indigo-900">Total Payable:</span>
                            <span class="text-xl font-black text-indigo-700">৳ <span x-text="grandTotal.toFixed(2)"></span></span>
                        </div>

                        <!-- Cash Input with Quick Chips -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-gray-700 uppercase">Cash Tendered (৳)</label>
                                <div class="flex items-center gap-1 text-[10px]">
                                    <button type="button" @click="splitCash = grandTotal.toFixed(2)" class="px-1.5 py-0.5 rounded bg-gray-100 hover:bg-gray-200 font-semibold">Exact</button>
                                    <button type="button" @click="splitCash = (500).toFixed(2)" class="px-1.5 py-0.5 rounded bg-gray-100 hover:bg-gray-200 font-semibold">500</button>
                                    <button type="button" @click="splitCash = (1000).toFixed(2)" class="px-1.5 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold">1000</button>
                                </div>
                            </div>
                            <input type="number" step="0.01" min="0" x-model="splitCash" class="w-full text-base font-bold rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Card Input -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Debit / Credit Card (৳)</label>
                            <input type="number" step="0.01" min="0" x-model="splitCard" class="w-full text-base font-bold rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- bKash / Mobile Banking -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">bKash (৳)</label>
                                <input type="number" step="0.01" min="0" x-model="splitBkash" class="w-full text-base font-bold rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nagad (৳)</label>
                                <input type="number" step="0.01" min="0" x-model="splitNagad" class="w-full text-base font-bold rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <!-- Prominent Reconciliation & Change Bar -->
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Total Tendered:</span>
                                <span class="font-bold text-gray-900">৳ <span x-text="totalPaidAmount.toFixed(2)"></span></span>
                            </div>
                            <template x-if="changeAmount > 0">
                                <div class="p-2.5 bg-emerald-500 text-white rounded-lg flex justify-between items-center shadow-sm">
                                    <span class="font-black text-xs uppercase tracking-wider text-emerald-100">Change to Return:</span>
                                    <span class="text-xl font-black">৳ <span x-text="changeAmount.toFixed(2)"></span></span>
                                </div>
                            </template>
                            <template x-if="dueAmount > 0">
                                <div class="flex justify-between font-bold text-red-600">
                                    <span>Remaining Due / Balance:</span>
                                    <span>৳ <span x-text="dueAmount.toFixed(2)"></span></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="paymentModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg">Cancel</button>
                        <button type="button" @click="submitCompleteCheckout()" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-lg shadow-sm">
                            Confirm & Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Held Carts Modal (F9) -->
        <div x-show="heldModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="heldModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-50"></div>
                <div class="inline-block bg-white rounded-xl shadow-2xl p-6 z-10 w-full max-w-lg">
                    <h3 class="text-base font-black text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <span>Held Carts</span>
                        <button @click="heldModalOpen = false" class="text-gray-400 font-bold">&times;</button>
                    </h3>

                    <div class="space-y-3 max-h-72 overflow-y-auto">
                        @forelse ($heldSales as $held)
                            <div class="p-3 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-between">
                                <div>
                                    <span class="font-mono text-xs font-bold text-indigo-700">{{ $held->reference_code }}</span>
                                    <div class="text-xs text-gray-500">{{ count($held->cart_data) }} items • ৳ {{ number_format($held->subtotal, 2) }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $held->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('pos.resume', $held->reference_code) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-md">Resume</button>
                                    </form>
                                    <form method="POST" action="{{ route('pos.delete_held', $held->reference_code) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-red-500 hover:text-red-700 text-xs font-bold">&times;</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-400 py-6">No held carts.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Member Register Modal -->
        <div x-show="memberModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="memberModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-50"></div>
                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-md">
                    <h3 class="text-base font-black text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <span>Quick Register Member</span>
                        <button @click="memberModalOpen = false" class="text-gray-400 font-bold">&times;</button>
                    </h3>

                    <div x-show="regError" class="mb-3 p-2.5 bg-red-50 text-red-700 text-xs rounded-lg" x-text="regError"></div>
                    <div x-show="regSuccess" class="mb-3 p-2.5 bg-emerald-50 text-emerald-700 text-xs rounded-lg" x-text="regSuccess"></div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name *</label>
                            <input x-model="newMemberName" placeholder="Customer Name" class="w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone Number</label>
                            <input x-model="newMemberPhone" placeholder="+8801700000000" class="w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email (Optional)</label>
                            <input x-model="newMemberEmail" type="email" placeholder="customer@mail.com" class="w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tier</label>
                            <select x-model="newMemberTier" class="w-full text-sm rounded-lg border-gray-300">
                                @foreach ($membershipTypes as $tier)
                                    <option value="{{ $tier->id }}">{{ $tier->name }} ({{ $tier->discount_percentage }}% Disc, {{ $tier->reward_points }} pts)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" @click="memberModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg">Cancel</button>
                        <button type="button" @click="registerMemberInline()" :disabled="regLoading" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg">
                            <span x-text="regLoading ? 'Registering...' : 'Save & Apply to Cart'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Shift Modal -->
        <div x-show="shiftModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="shiftModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-50"></div>
                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-sm">
                    <h3 class="text-base font-black text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <span>Open Daily Cash Shift</span>
                        <button @click="shiftModalOpen = false" class="text-gray-400 font-bold">&times;</button>
                    </h3>
                    <form method="POST" action="{{ route('pos.open_shift') }}">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Opening Cash (৳)</label>
                                <input name="opening_balance" type="number" step="0.01" min="0" value="0.00" class="w-full text-base font-bold rounded-lg border-gray-300" required autofocus>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Notes</label>
                                <input name="notes" placeholder="e.g. Morning counter shift" class="w-full text-xs rounded-lg border-gray-300">
                            </div>
                        </div>
                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" @click="shiftModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg">Open Shift</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Close Shift Modal -->
        <div x-show="closeShiftModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="closeShiftModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-50"></div>
                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-md">
                    <h3 class="text-base font-black text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <span>Close Current Shift</span>
                        <button @click="closeShiftModalOpen = false" class="text-gray-400 font-bold">&times;</button>
                    </h3>
                    @if ($activeShift)
                        <form method="POST" action="{{ route('pos.close_shift') }}">
                            @csrf
                            <div class="space-y-3">
                                <div class="p-3 bg-gray-50 rounded-lg text-xs space-y-1">
                                    <div class="flex justify-between text-gray-600"><span>Opening Cash:</span><span class="font-bold">৳ {{ number_format($activeShift->opening_balance, 2) }}</span></div>
                                    <div class="flex justify-between text-gray-600"><span>Cash Sales:</span><span class="font-bold text-emerald-600">+ ৳ {{ number_format($activeShift->cash_sales, 2) }}</span></div>
                                    <div class="flex justify-between text-gray-600"><span>Cash Refunds:</span><span class="font-bold text-red-600">- ৳ {{ number_format($activeShift->cash_refunds, 2) }}</span></div>
                                    <div class="flex justify-between text-gray-600"><span>Cash Expenses:</span><span class="font-bold text-red-600">- ৳ {{ number_format($activeShift->cash_expenses, 2) }}</span></div>
                                    <div class="flex justify-between font-black text-indigo-900 pt-1 border-t border-gray-200"><span>Expected Balance:</span><span>৳ {{ number_format($activeShift->expected_balance, 2) }}</span></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Counted Physical Cash in Drawer (৳) *</label>
                                    <input name="actual_balance" type="number" step="0.01" min="0" value="{{ number_format($activeShift->expected_balance, 2, '.', '') }}" class="w-full text-base font-bold rounded-lg border-gray-300" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Closing Notes</label>
                                    <input name="notes" placeholder="Shift closing notes" class="w-full text-xs rounded-lg border-gray-300">
                                </div>
                            </div>
                            <div class="mt-5 flex justify-end gap-2">
                                <button type="button" @click="closeShiftModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg">Cancel</button>
                                <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg">Confirm Close Shift</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Branch Manager Password Authorization Modal -->
        <div x-show="managerModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="managerModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-xs"></div>
                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-md border border-amber-200">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black">
                                🔑
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900">Branch Manager Authorization</h3>
                                <p class="text-[11px] text-gray-500">Authorized for {{ Auth::user()->branch?->name ?? 'Active Branch' }}</p>
                            </div>
                        </div>
                        <button @click="managerModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
                    </div>

                    <div x-show="managerAuthError" class="mt-3 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-semibold rounded-r">
                        <span x-text="managerAuthError"></span>
                    </div>

                    <div x-show="managerAuthSuccess" class="mt-3 p-3 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-bold rounded-r">
                        <span x-text="managerAuthSuccess"></span>
                    </div>

                    <form @submit.prevent="submitManagerAuth()" class="mt-4 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Authorization Purpose</label>
                            <select x-model="managerAction" class="w-full text-xs font-semibold rounded-lg border-gray-300">
                                <option value="backoffice">Unlock Full Back-Office &amp; Dashboard (15 Mins)</option>
                                <option value="discount_override">Manual Discount / Price Override</option>
                                <option value="void_order">Void Entire Current Cart</option>
                                <option value="stock_transfer">Initiate Stock Transfer</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Branch Manager Email (Optional)</label>
                            <input type="email" x-model="managerEmail" placeholder="e.g. manager@onestop.com (leave blank for any outlet manager)" class="w-full text-xs rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Manager Password *</label>
                            <input type="password" x-model="managerPassword" placeholder="Enter Branch Manager Password" class="w-full text-sm font-bold rounded-lg border-gray-300 focus:ring-amber-500 focus:border-amber-500" required autofocus>
                        </div>

                        <div class="pt-2 flex justify-end gap-2">
                            <button type="button" @click="managerModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50">Cancel</button>
                            <button type="submit" :disabled="managerAuthLoading" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                                <span x-text="managerAuthLoading ? 'Verifying...' : 'Authorize Action'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

