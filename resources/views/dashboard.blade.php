<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('OneStop Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm text-gray-500">Today's Sales</p>
                    <p class="mt-2 text-3xl font-bold text-gray-800">৳ {{ number_format($todaySales ?? 0, 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm text-gray-500">Today's Purchases</p>
                    <p class="mt-2 text-3xl font-bold text-gray-800">৳ {{ number_format($todayPurchases ?? 0, 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm text-gray-500">Today's Expenses</p>
                    <p class="mt-2 text-3xl font-bold text-gray-800">৳ {{ number_format($todayExpenses ?? 0, 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm text-gray-500">Today's Profit</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-600">৳ {{ number_format($todayProfit ?? 0, 2) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-5">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Inventory Snapshot</h3>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex justify-between"><span>Total products</span><span>{{ $totalProducts ?? 0 }}</span></li>
                        <li class="flex justify-between"><span>Low stock</span><span>{{ $lowStockProducts ?? 0 }}</span></li>
                        <li class="flex justify-between"><span>Out of stock</span><span>{{ $outOfStockProducts ?? 0 }}</span></li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Customer & Membership</h3>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex justify-between"><span>Total customers</span><span>{{ $totalCustomers ?? 0 }}</span></li>
                        <li class="flex justify-between"><span>Total members</span><span>{{ $totalMembers ?? 0 }}</span></li>
                        <li class="flex justify-between"><span>Total suppliers</span><span>{{ $totalSuppliers ?? 0 }}</span></li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Operations</h3>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex justify-between"><span>Today's transactions</span><span>{{ $todayTransactions ?? 0 }}</span></li>
                        <li class="flex justify-between"><span>Open POS</span><span><a href="{{ route('pos.index') }}" class="text-indigo-600">Go to POS</a></span></li>
                        <li class="flex justify-between"><span>Reports</span><span><a href="{{ route('reports.index') }}" class="text-indigo-600">View</a></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
