<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Customers</h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="POST" action="{{ route('customers.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <input name="customer_id" placeholder="Customer ID" class="border rounded p-2" required>
                <input name="name" placeholder="Customer name" class="border rounded p-2" required>
                <input name="phone" placeholder="Phone" class="border rounded p-2">
                <input name="email" type="email" placeholder="Email" class="border rounded p-2">
                <input name="address" placeholder="Address" class="border rounded p-2 md:col-span-2">
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-3">Add customer</button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Phone</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="px-4 py-3">{{ $customer->name }}</td>
                            <td class="px-4 py-3">{{ $customer->phone }}</td>
                            <td class="px-4 py-3">{{ $customer->email ?? '-' }}</td>
                            <td class="px-4 py-3">৳ {{ number_format($customer->current_balance, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
