<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500">Total Sales</p>
                <p class="mt-2 text-2xl font-bold">৳ {{ number_format($totalSales ?? 0, 2) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500">Total Purchases</p>
                <p class="mt-2 text-2xl font-bold">৳ {{ number_format($totalPurchases ?? 0, 2) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500">Gross Profit</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">৳ {{ number_format($grossProfit ?? 0, 2) }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-5">
                <p class="text-sm text-gray-500">Out of Stock</p>
                <p class="mt-2 text-2xl font-bold text-red-600">{{ $outOfStockProducts ?? 0 }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
