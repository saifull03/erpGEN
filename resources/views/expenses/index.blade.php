<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Expense Management</h2>
                <p class="text-sm text-gray-500 mt-1">Track store operational overhead, utilities, maintenance, and staff costs.</p>
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

        <!-- Forms: Record Expense + Add Category Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Record Expense -->
            <div class="lg:col-span-2 bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    Record New Expense
                </h3>
                <form method="POST" action="{{ route('expenses.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Expense Category <span class="text-red-500">*</span></label>
                        <select name="category" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->name }}" {{ old('category') === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                            @if ($categories->isEmpty())
                                <option value="Electricity">Electricity</option>
                                <option value="Salary">Salary</option>
                                <option value="Rent">Rent</option>
                                <option value="Maintenance">Maintenance</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Amount (৳) <span class="text-red-500">*</span></label>
                        <input name="amount" type="number" step="0.01" value="{{ old('amount') }}" placeholder="0.00" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="cash">Cash (Updates Active Shift)</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Date <span class="text-red-500">*</span></label>
                        <input name="date" type="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Reference / Voucher No.</label>
                        <input name="reference" value="{{ old('reference') }}" placeholder="e.g. BILL-98234" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description / Note <span class="text-red-500">*</span></label>
                        <input name="description" value="{{ old('description') }}" placeholder="e.g. Monthly DESCO Electricity bill" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div class="sm:col-span-2 flex justify-end pt-2">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Record Expense
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right 1 Col: Quick Add Expense Category -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 h-fit">
                <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    New Expense Category
                </h3>
                <form method="POST" action="{{ route('expenses.categories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Category Name <span class="text-red-500">*</span></label>
                        <input name="name" placeholder="e.g. Internet, Refreshment" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description</label>
                        <input name="description" placeholder="Brief details" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-gray-800 hover:bg-gray-900 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        Add Category
                    </button>
                </form>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4">
            <form method="GET" action="{{ route('expenses.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Category Filter</label>
                    <select name="category" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['category', 'start_date', 'end_date']))
                        <a href="{{ route('expenses.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Expenses List Table -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-base">Recorded Expenses</h3>
                <span class="text-xs text-gray-500">{{ $expenses->total() ?? count($expenses) }} records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Expense ID</th>
                            <th class="px-5 py-3.5 text-left">Date</th>
                            <th class="px-5 py-3.5 text-left">Category</th>
                            <th class="px-5 py-3.5 text-left">Description</th>
                            <th class="px-5 py-3.5 text-left">Payment Method</th>
                            <th class="px-5 py-3.5 text-left">Created By</th>
                            <th class="px-5 py-3.5 text-right">Amount</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($expenses as $expense)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-3.5 font-mono text-xs font-bold text-gray-800">
                                    {{ $expense->expense_id }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-600 text-xs">
                                    {{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        {{ $expense->category }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-800 font-medium max-w-xs">
                                    <div>{{ $expense->description }}</div>
                                    @if ($expense->reference)
                                        <div class="text-[11px] text-gray-400 font-mono">Ref: {{ $expense->reference }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-600 uppercase font-semibold">
                                    {{ str_replace('_', ' ', $expense->payment_method) }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-500">
                                    {{ $expense->createdBy?->name ?? 'System' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-extrabold text-gray-900">
                                    ৳ {{ number_format($expense->amount, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete expense {{ $expense->expense_id }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs text-red-600 hover:text-red-800 font-semibold" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-gray-400">No expenses recorded matching criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($expenses->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
