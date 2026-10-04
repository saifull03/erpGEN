<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Discounts & Promotions</h2>
                <p class="text-sm text-gray-500 mt-1">Configure campaign discounts, promo codes, percentage/fixed deals, and BOGO promotions.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
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

        <!-- Create Promotion Form -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
            <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Create Promotional Offer
            </h3>
            <form method="POST" action="{{ route('promotions.store') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Promotion Title <span class="text-red-500">*</span></label>
                    <input name="title" value="{{ old('title') }}" placeholder="e.g. Ramadan Special 10% Off" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Coupon Code (Optional)</label>
                    <input name="code" value="{{ old('code') }}" placeholder="e.g. RAMADAN10" class="w-full text-sm border-gray-300 rounded-lg shadow-sm font-mono uppercase focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Discount Type <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="percentage">Percentage (%) Discount</option>
                        <option value="fixed">Fixed Amount (৳) Off</option>
                        <option value="bogo">Buy One Get One (BOGO)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Discount Value (% or ৳) <span class="text-red-500">*</span></label>
                    <input name="value" type="number" step="0.01" value="{{ old('value') }}" placeholder="e.g. 10 or 100" class="w-full text-sm border-gray-300 rounded-lg shadow-sm font-bold focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Minimum Spend (৳)</label>
                    <input name="min_spend" type="number" step="0.01" value="{{ old('min_spend', '0') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Start Date</label>
                    <input name="start_date" type="date" value="{{ old('start_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">End Date</label>
                    <input name="end_date" type="date" value="{{ old('end_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-center pt-5">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                        <span class="ml-2 text-xs font-semibold text-gray-700 uppercase">Active Immediately</span>
                    </label>
                </div>
                <div class="sm:col-span-2 md:col-span-3 lg:col-span-4 flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Save Promotion
                    </button>
                </div>
            </form>
        </div>

        <!-- Promotions Directory Table -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-base">Configured Promotions & Discount Rules</h3>
                <span class="text-xs text-gray-500">{{ $promotions->total() ?? count($promotions) }} promotions</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Promotion Title</th>
                            <th class="px-5 py-3.5 text-left">Code</th>
                            <th class="px-5 py-3.5 text-left">Discount Type</th>
                            <th class="px-5 py-3.5 text-right">Value</th>
                            <th class="px-5 py-3.5 text-right">Min Spend</th>
                            <th class="px-5 py-3.5 text-left">Validity</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($promotions as $promo)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-4 font-bold text-gray-900">
                                    {{ $promo->title }}
                                </td>
                                <td class="px-5 py-4 font-mono text-xs font-bold text-indigo-600">
                                    {{ $promo->code ?? 'AUTOMATIC' }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 capitalize">
                                        {{ $promo->type }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right font-extrabold text-gray-900">
                                    @if ($promo->type === 'percentage')
                                        {{ $promo->value }}%
                                    @else
                                        ৳ {{ number_format($promo->value, 2) }}
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right text-gray-600 text-xs font-medium">
                                    ৳ {{ number_format($promo->min_spend, 2) }}
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    @if ($promo->start_date || $promo->end_date)
                                        {{ $promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->format('M d') : 'Any' }} &rarr;
                                        {{ $promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->format('M d, Y') : 'No expiry' }}
                                    @else
                                        Always Active
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <form method="POST" action="{{ route('promotions.toggle', $promo) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold cursor-pointer transition {{ $promo->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-100 text-rose-800 hover:bg-rose-200' }}">
                                            {{ $promo->is_active ? 'Active' : 'Disabled' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('promotions.destroy', $promo) }}" onsubmit="return confirm('Delete promotion {{ $promo->title }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs text-red-600 hover:text-red-800 font-semibold" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-gray-400">No promotions or discounts active.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($promotions->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $promotions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
