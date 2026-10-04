<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Supermarket ERP Reports</h2>
                <p class="text-sm text-gray-500 mt-1">Analytical reporting across sales, procurement, COGS profit & loss, inventory valuation, and member loyalty.</p>
            </div>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Report
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Navigation Tabs -->
        <div class="flex border-b border-gray-200 overflow-x-auto gap-2 text-sm font-medium">
            <a href="{{ route('reports.index', ['tab' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold {{ $tab === 'sales' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Sales Analytics
            </a>
            <a href="{{ route('reports.index', ['tab' => 'purchases', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold {{ $tab === 'purchases' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Purchases & Procurement
            </a>
            <a href="{{ route('reports.index', ['tab' => 'profit_loss', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold {{ $tab === 'profit_loss' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Profit & Loss (P&L)
            </a>
            <a href="{{ route('reports.index', ['tab' => 'inventory']) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold {{ $tab === 'inventory' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Inventory Valuation & Alerts
            </a>
            <a href="{{ route('reports.index', ['tab' => 'dues']) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold {{ $tab === 'dues' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Receivables & Payables (Dues)
            </a>
            <a href="{{ route('reports.index', ['tab' => 'members']) }}"
               class="pb-3 px-4 whitespace-nowrap border-b-2 font-bold {{ $tab === 'members' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Membership & Loyalty
            </a>
        </div>

        <!-- Date Range Filter (For Sales, Purchases, P&L) -->
        @if (in_array($tab, ['sales', 'purchases', 'profit_loss']))
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">From Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">To Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    @if ($tab === 'sales' && isset($cashiers))
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cashier Filter</label>
                            <select name="user_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Cashiers</option>
                                @foreach ($cashiers as $c)
                                    <option value="{{ $c->id }}" {{ request('user_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif ($tab === 'purchases' && isset($suppliers))
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Supplier Filter</label>
                            <select name="supplier_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Suppliers</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div></div>
                    @endif
                    <div>
                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- TAB 1: SALES REPORT -->
        @if ($tab === 'sales' && isset($salesData))
            <div class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Gross Sales</div>
                        <div class="text-2xl font-black text-gray-900 mt-1">৳ {{ number_format($salesData['total_sales'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Total Discounts Given</div>
                        <div class="text-2xl font-black text-amber-600 mt-1">৳ {{ number_format($salesData['total_discount'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Net Sales Revenue</div>
                        <div class="text-2xl font-black text-emerald-600 mt-1">৳ {{ number_format($salesData['net_sales'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Completed Transactions</div>
                        <div class="text-2xl font-black text-indigo-600 mt-1">{{ $salesData['total_orders'] ?? 0 }} orders</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 text-base mb-4">Daily Sales Breakdown</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-4 py-3 text-left">Date</th>
                                    <th class="px-4 py-3 text-right">Orders</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                    <th class="px-4 py-3 text-right">Discount</th>
                                    <th class="px-4 py-3 text-right">VAT/Tax</th>
                                    <th class="px-4 py-3 text-right font-bold text-gray-900">Net Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($salesData['sales_by_date'] ?? [] as $dateRow)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ \Carbon\Carbon::parse($dateRow->date)->format('M d, Y (l)') }}</td>
                                        <td class="px-4 py-3 text-right font-mono">{{ $dateRow->orders_count }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">৳ {{ number_format($dateRow->subtotal_sum, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-amber-600">৳ {{ number_format($dateRow->discount_sum, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">৳ {{ number_format($dateRow->tax_sum, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-emerald-600">৳ {{ number_format($dateRow->net_sum, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">No sales records in selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 2: PURCHASES REPORT -->
        @if ($tab === 'purchases' && isset($purchaseData))
            <div class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Total Purchases</div>
                        <div class="text-2xl font-black text-gray-900 mt-1">৳ {{ number_format($purchaseData['total_purchases'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Total Paid Amount</div>
                        <div class="text-2xl font-black text-emerald-600 mt-1">৳ {{ number_format($purchaseData['total_paid'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Outstanding Vendor Due</div>
                        <div class="text-2xl font-black text-rose-600 mt-1">৳ {{ number_format($purchaseData['total_due'] ?? 0, 2) }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 text-base mb-4">Supplier-Wise Procurement Summary</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-4 py-3 text-left">Supplier</th>
                                    <th class="px-4 py-3 text-right">Invoices</th>
                                    <th class="px-4 py-3 text-right">Total Billed</th>
                                    <th class="px-4 py-3 text-right">Paid</th>
                                    <th class="px-4 py-3 text-right text-rose-600">Due</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($purchaseData['purchases_by_supplier'] ?? [] as $suppRow)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 font-bold text-gray-900">{{ $suppRow->supplier?->company_name ?? 'Vendor' }}</td>
                                        <td class="px-4 py-3 text-right font-mono">{{ $suppRow->invoices_count }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-900">৳ {{ number_format($suppRow->total_amount, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-emerald-600">৳ {{ number_format($suppRow->paid_amount, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-rose-600">৳ {{ number_format($suppRow->due_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">No purchase invoices found in selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 3: PROFIT & LOSS REPORT -->
        @if ($tab === 'profit_loss' && isset($plData))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Profit & Loss Statement (Income Statement)</h3>
                    <p class="text-xs text-gray-500">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
                </div>

                <div class="border rounded-xl overflow-hidden divide-y divide-gray-200">
                    <div class="p-4 bg-gray-50 flex justify-between font-bold text-gray-800 text-base">
                        <span>Revenue / Gross Sales</span>
                        <span>৳ {{ number_format($plData['revenue'] ?? 0, 2) }}</span>
                    </div>
                    <div class="p-4 flex justify-between text-gray-700">
                        <span>Cost of Goods Sold (COGS at Purchase Cost)</span>
                        <span class="text-rose-600">- ৳ {{ number_format($plData['cogs'] ?? 0, 2) }}</span>
                    </div>
                    <div class="p-4 bg-emerald-50/50 flex justify-between font-bold text-emerald-900 text-base">
                        <span>Gross Profit</span>
                        <span>৳ {{ number_format($plData['gross_profit'] ?? 0, 2) }}</span>
                    </div>
                    <div class="p-4 flex justify-between text-gray-700">
                        <span>Operational Expenses (Electricity, Salary, Rent, Maintenance, etc.)</span>
                        <span class="text-rose-600">- ৳ {{ number_format($plData['expenses'] ?? 0, 2) }}</span>
                    </div>
                    <div class="p-5 bg-indigo-900 text-white flex justify-between font-black text-xl">
                        <span>Net Operating Profit</span>
                        <span class="{{ ($plData['net_profit'] ?? 0) >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                            ৳ {{ number_format($plData['net_profit'] ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 4: INVENTORY VALUATION & ALERTS -->
        @if ($tab === 'inventory' && isset($valuation))
            <div class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Total Active Items</div>
                        <div class="text-2xl font-black text-gray-900 mt-1">{{ $valuation['total_items'] ?? 0 }} SKUs</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Total Purchase Cost Valuation</div>
                        <div class="text-2xl font-black text-indigo-700 mt-1">৳ {{ number_format($valuation['total_cost_value'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Total Retail Selling Valuation</div>
                        <div class="text-2xl font-black text-emerald-600 mt-1">৳ {{ number_format($valuation['total_retail_value'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs uppercase font-semibold text-gray-400">Potential Gross Margin</div>
                        <div class="text-2xl font-black text-blue-600 mt-1">৳ {{ number_format($valuation['potential_margin'] ?? 0, 2) }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Low Stock Alerts -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h4 class="font-bold text-gray-900 text-sm mb-3 text-amber-700 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Low & Out of Stock Products
                        </h4>
                        <div class="space-y-2 max-h-80 overflow-y-auto">
                            @forelse ($lowStock ?? [] as $p)
                                <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg text-xs">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $p->name }}</div>
                                        <div class="text-gray-400 font-mono">{{ $p->sku }}</div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full font-bold {{ $p->current_stock <= 0 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $p->current_stock }} left (Min: {{ $p->minimum_stock }})
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-6 text-xs text-gray-400">All products have healthy inventory levels.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Expiring Products -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h4 class="font-bold text-gray-900 text-sm mb-3 text-purple-700 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Expiring Within 30 Days
                        </h4>
                        <div class="space-y-2 max-h-80 overflow-y-auto">
                            @forelse ($expiring ?? [] as $exp)
                                <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg text-xs">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $exp->name }}</div>
                                        <div class="text-gray-400 font-mono">Batch: {{ $exp->batch_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-purple-700">{{ \Carbon\Carbon::parse($exp->expiry_date)->format('M d, Y') }}</div>
                                        <div class="text-gray-400">{{ $exp->current_stock }} units in stock</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-xs text-gray-400">No products expiring in next 30 days.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 5: DUES (RECEIVABLES & PAYABLES) -->
        @if ($tab === 'dues')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Customer Receivables -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 text-base mb-4 text-emerald-800">Customer Dues (Receivables)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">Customer</th>
                                    <th class="px-4 py-2.5 text-left">Phone</th>
                                    <th class="px-4 py-2.5 text-right">Due Balance</th>
                                    <th class="px-4 py-2.5 text-right">Ledger</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($customerDues ?? [] as $cus)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $cus->name }}</td>
                                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $cus->phone ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-rose-600">৳ {{ number_format($cus->current_balance, 2) }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('customers.ledger', $cus) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No outstanding customer dues.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Supplier Payables -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 text-base mb-4 text-blue-800">Supplier Dues (Payables)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">Supplier</th>
                                    <th class="px-4 py-2.5 text-left">Contact</th>
                                    <th class="px-4 py-2.5 text-right">Payable Balance</th>
                                    <th class="px-4 py-2.5 text-right">Ledger</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($supplierDues ?? [] as $sup)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $sup->company_name }}</td>
                                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $sup->phone ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-rose-600">৳ {{ number_format($sup->current_payable_balance, 2) }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('suppliers.ledger', $sup) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No outstanding supplier payables.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 6: MEMBERSHIP LOYALTY -->
        @if ($tab === 'members' && isset($members))
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 text-base mb-4">Membership Tier & Loyalty Points Rankings</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-5 py-3.5 text-left">Member ID</th>
                                <th class="px-5 py-3.5 text-left">Customer Name</th>
                                <th class="px-5 py-3.5 text-left">Membership Tier</th>
                                <th class="px-5 py-3.5 text-right">Loyalty Points</th>
                                <th class="px-5 py-3.5 text-right">Total Purchases</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($members as $m)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-4 font-mono font-bold text-indigo-600 text-xs">{{ $m->member_number }}</td>
                                    <td class="px-5 py-4 font-semibold text-gray-900">{{ $m->customer?->name ?? 'Member' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            {{ $m->membershipType?->name ?? 'Standard' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right font-extrabold text-indigo-600">{{ number_format($m->points) }} pts</td>
                                    <td class="px-5 py-4 text-right font-semibold text-gray-900">৳ {{ number_format($m->total_purchase_amount, 2) }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $m->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($m->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('members.card', $m) }}" target="_blank" class="text-xs text-indigo-600 hover:underline font-semibold mr-3">Card</a>
                                        <a href="{{ route('members.points', $m) }}" class="text-xs text-gray-600 hover:underline">Points Log</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-gray-400">No member accounts registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($members->hasPages())
                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 mt-4">
                        {{ $members->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
