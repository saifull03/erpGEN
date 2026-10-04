<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suppliers</h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="POST" action="{{ route('suppliers.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <input name="supplier_id" placeholder="Supplier ID" class="border rounded p-2" required>
                <input name="company_name" placeholder="Company name" class="border rounded p-2" required>
                <input name="contact_person" placeholder="Contact person" class="border rounded p-2">
                <input name="phone" placeholder="Phone" class="border rounded p-2">
                <input name="email" type="email" placeholder="Email" class="border rounded p-2">
                <input name="opening_balance" type="number" step="0.01" placeholder="Opening balance" class="border rounded p-2">
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-3">Add supplier</button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Supplier</th>
                        <th class="px-4 py-3 text-left">Company</th>
                        <th class="px-4 py-3 text-left">Phone</th>
                        <th class="px-4 py-3 text-left">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td class="px-4 py-3">{{ $supplier->supplier_id }}</td>
                            <td class="px-4 py-3">{{ $supplier->company_name }}</td>
                            <td class="px-4 py-3">{{ $supplier->phone }}</td>
                            <td class="px-4 py-3">৳ {{ number_format($supplier->current_payable_balance, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
