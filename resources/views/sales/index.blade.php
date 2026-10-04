<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Cashier</th>
                        <th class="px-4 py-3 text-left">Member</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td class="px-4 py-3">{{ $sale->invoice_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $sale->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $sale->user?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $sale->member?->member_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3">৳ {{ number_format($sale->grand_total ?? 0, 2) }}</td>
                            <td class="px-4 py-3">{{ $sale->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
