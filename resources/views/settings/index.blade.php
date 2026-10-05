<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">System & Store Settings</h2>
                <p class="text-sm text-gray-500 mt-1">Configure erpGEN supermarket identity, receipt metadata, currency, tax rates, and loyalty points.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-emerald-800 font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('settings.store') }}" class="space-y-6">
            @csrf

            <!-- Store Information -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Supermarket Identity & Contact
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Store / Business Name <span class="text-red-500">*</span></label>
                        <input name="store_name" value="{{ old('store_name', $settings['store_name'] ?? 'erpGEN Supermarket') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Official Email</label>
                        <input name="store_email" type="email" value="{{ old('store_email', $settings['store_email'] ?? 'support@erpgen.local') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Helpline Phone</label>
                        <input name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? '+880 1700-000000') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Store Address</label>
                        <input name="store_address" value="{{ old('store_address', $settings['store_address'] ?? 'Plot 12, Road 4, Gulshan-2, Dhaka-1212') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Financial, Tax & Currency Settings -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Currency, Taxes & Invoice Configuration
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Currency Symbol <span class="text-red-500">*</span></label>
                        <input name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '৳') }}" class="w-full text-sm font-bold border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Currency Code <span class="text-red-500">*</span></label>
                        <input name="currency_code" value="{{ old('currency_code', $settings['currency_code'] ?? 'BDT') }}" class="w-full text-sm font-bold border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Default Tax / VAT (%)</label>
                        <input name="tax_rate" type="number" step="0.01" value="{{ old('tax_rate', $settings['tax_rate'] ?? '5.00') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Invoice Prefix</label>
                        <input name="invoice_prefix" value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-') }}" class="w-full text-sm font-mono border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Loyalty & Inventory Rules -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Loyalty Rewards & Thermal Receipt
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Points Earned per ৳ 100 Spend</label>
                        <input name="loyalty_points_per_hundred" type="number" value="{{ old('loyalty_points_per_hundred', $settings['loyalty_points_per_hundred'] ?? '1') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="text-[11px] text-gray-400 mt-1">Example: 1 means ৳ 100 spend gives 1 loyalty point.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Discount (৳) per 100 Points</label>
                        <input name="loyalty_discount_per_hundred_points" type="number" step="0.01" value="{{ old('loyalty_discount_per_hundred_points', $settings['loyalty_discount_per_hundred_points'] ?? '150.00') }}" class="w-full text-sm font-bold text-emerald-700 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="text-[11px] text-gray-400 mt-1">Example: 150 means 100 points = ৳ 150 discount.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Low Stock Alert Threshold</label>
                        <input name="low_stock_threshold" type="number" value="{{ old('low_stock_threshold', $settings['low_stock_threshold'] ?? '5') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="text-[11px] text-gray-400 mt-1">Default minimum quantity trigger per item.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Thermal Receipt Footer Message</label>
                        <textarea name="receipt_footer" rows="2" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('receipt_footer', $settings['receipt_footer'] ?? 'Thank you for shopping at erpGEN Supermarket! Please visit us again.') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-md transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save All Settings
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
