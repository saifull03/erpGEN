<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchases</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-left">Due</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchases as $purchase)
                        <tr>
                            <td class="px-4 py-3">{{ $purchase->purchase_invoice_number }}</td>
                            <td class="px-4 py-3">{{ $purchase->supplier?->company_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $purchase->date }}</td>
                            <td class="px-4 py-3">৳ {{ number_format($purchase->total, 2) }}</td>
                            <td class="px-4 py-3">৳ {{ number_format($purchase->due, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
