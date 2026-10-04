<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Cashier Shift & Cash Management</h2>
                <p class="text-sm text-gray-500 mt-1">Track cash drawer sessions, sales, refunds, expenses, and cash reconciliation.</p>
            </div>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Go to POS
            </a>
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

        <!-- Active Shift Section -->
        @if ($activeShift)
            <div class="bg-gradient-to-r from-emerald-900 to-teal-900 text-white rounded-2xl p-6 shadow-md border border-emerald-800">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500 text-emerald-950 uppercase tracking-wider mb-2">
                            ● Active Shift Open
                        </div>
                        <h3 class="text-xl font-bold">Shift Session for {{ $activeShift->user?->name }}</h3>
                        <p class="text-xs text-emerald-200 mt-0.5">Started at {{ $activeShift->opened_at ? $activeShift->opened_at->format('M d, Y - h:i A') : 'N/A' }}</p>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5">
                            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                                <div class="text-[11px] uppercase tracking-wider text-emerald-200 font-medium">Opening Cash</div>
                                <div class="text-lg font-extrabold mt-1">৳ {{ number_format($activeShift->opening_balance, 2) }}</div>
                            </div>
                            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                                <div class="text-[11px] uppercase tracking-wider text-emerald-200 font-medium">Cash Sales</div>
                                <div class="text-lg font-extrabold text-emerald-300 mt-1">+ ৳ {{ number_format($activeShift->cash_sales, 2) }}</div>
                            </div>
                            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                                <div class="text-[11px] uppercase tracking-wider text-emerald-200 font-medium">Refunds & Exp</div>
                                <div class="text-lg font-extrabold text-amber-300 mt-1">- ৳ {{ number_format($activeShift->cash_refunds + $activeShift->cash_expenses, 2) }}</div>
                            </div>
                            <div class="bg-emerald-500 text-emerald-950 rounded-xl p-3 shadow-inner">
                                <div class="text-[11px] uppercase tracking-wider font-bold">Expected In Drawer</div>
                                <div class="text-xl font-black mt-1">৳ {{ number_format($activeShift->expected_balance, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Close Shift Form -->
                    <div class="bg-white text-gray-900 rounded-xl p-5 shadow-lg max-w-sm w-full">
                        <h4 class="font-bold text-sm text-gray-900 mb-2">Reconcile & Close Shift</h4>
                        <form method="POST" action="{{ route('shifts.close', $activeShift) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Physical Counted Cash (৳) <span class="text-red-500">*</span></label>
                                <input name="actual_balance" type="number" step="0.01" placeholder="Enter counted cash in drawer" class="w-full text-sm font-bold border-gray-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Closing Notes / Discrepancy Reason</label>
                                <input name="notes" placeholder="e.g. Exact match or shortage note" class="w-full text-xs border-gray-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <button type="submit" class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-lg shadow-sm transition">
                                Close Drawer & End Shift
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Open New Shift Card -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                <div class="max-w-xl">
                    <h3 class="font-bold text-gray-900 text-lg mb-1 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Start Your POS Shift
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">No active shift session found for your account. Please record your opening cash float to begin processing sales.</p>
                    <form method="POST" action="{{ route('shifts.open') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Opening Cash Float (৳) <span class="text-red-500">*</span></label>
                            <input name="opening_balance" type="number" step="0.01" value="0.00" placeholder="e.g. 500.00" class="w-full text-sm font-bold border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Notes / Register ID</label>
                            <input name="notes" placeholder="Counter #1 Float" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-lg shadow-sm transition">
                                Open Register Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Shift History Filter -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4">
            <form method="GET" action="{{ route('shifts.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Filter Cashier</label>
                    <select name="user_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                        <option value="">All Staff Members</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                    <select name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                        <option value="">All Shifts</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['user_id', 'status']))
                        <a href="{{ route('shifts.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Shift History Table -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-base">Shift Logs & Cash Reconciliation History</h3>
                <span class="text-xs text-gray-500">{{ $shifts->total() ?? count($shifts) }} shifts</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Cashier / Staff</th>
                            <th class="px-5 py-3.5 text-left">Period</th>
                            <th class="px-5 py-3.5 text-right">Opening</th>
                            <th class="px-5 py-3.5 text-right">Cash Sales</th>
                            <th class="px-5 py-3.5 text-right">Expected</th>
                            <th class="px-5 py-3.5 text-right">Counted Actual</th>
                            <th class="px-5 py-3.5 text-right">Difference</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($shifts as $s)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-4 font-semibold text-gray-900">
                                    {{ $s->user?->name ?? 'Staff' }}
                                    @if ($s->notes)
                                        <div class="text-[11px] text-gray-400 font-normal">{{ $s->notes }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">
                                    <div>{{ $s->opened_at ? $s->opened_at->format('M d, Y h:i A') : '—' }}</div>
                                    <div class="text-gray-400">Closed: {{ $s->closed_at ? $s->closed_at->format('M d, Y h:i A') : 'In progress' }}</div>
                                </td>
                                <td class="px-5 py-4 text-right font-medium text-gray-700">
                                    ৳ {{ number_format($s->opening_balance, 2) }}
                                </td>
                                <td class="px-5 py-4 text-right font-medium text-emerald-600">
                                    ৳ {{ number_format($s->cash_sales, 2) }}
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-gray-900">
                                    ৳ {{ number_format($s->expected_balance, 2) }}
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-gray-900">
                                    {{ $s->actual_balance !== null ? '৳ ' . number_format($s->actual_balance, 2) : '—' }}
                                </td>
                                <td class="px-5 py-4 text-right font-bold">
                                    @if ($s->status === 'closed')
                                        @if ($s->difference == 0)
                                            <span class="text-emerald-600">৳ 0.00</span>
                                        @elseif ($s->difference > 0)
                                            <span class="text-blue-600">+৳ {{ number_format($s->difference, 2) }}</span>
                                        @else
                                            <span class="text-rose-600">-৳ {{ number_format(abs($s->difference), 2) }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s->status === 'open' ? 'bg-emerald-100 text-emerald-800 animate-pulse' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($s->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-gray-400">No shift records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($shifts->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
