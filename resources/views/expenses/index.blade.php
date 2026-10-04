<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Expenses</h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="POST" action="{{ route('expenses.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <input name="expense_id" placeholder="Expense ID" class="border rounded p-2" required>
                <input name="category" placeholder="Category" class="border rounded p-2" required>
                <input name="amount" type="number" step="0.01" placeholder="Amount" class="border rounded p-2" required>
                <input name="description" placeholder="Description" class="border rounded p-2 md:col-span-2">
                <input name="date" type="date" class="border rounded p-2">
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-3">Save expense</button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Expense</th>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr>
                            <td class="px-4 py-3">{{ $expense->expense_id }}</td>
                            <td class="px-4 py-3">{{ $expense->category }}</td>
                            <td class="px-4 py-3">{{ $expense->date }}</td>
                            <td class="px-4 py-3">৳ {{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
