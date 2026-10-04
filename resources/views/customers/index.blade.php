<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Customer Management</h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage customer directory, balances, and membership enrollments.</p>
            </div>
            <a href="{{ route('members.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-100 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                Register Membership
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center">
                <svg class="w-6 h-6 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 mb-8">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                Add New Customer
            </h3>
            <form method="POST" action="{{ route('customers.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Customer ID</label>
                    <input name="customer_id" value="{{ old('customer_id') }}" placeholder="Auto generated (e.g. CUS-001)" class="w-full text-sm border-gray-300 rounded-lg shadow-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Name <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name') }}" placeholder="Customer full name" class="w-full text-sm border-gray-300 rounded-lg shadow-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="Phone number" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="Email address" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Address</label>
                    <input name="address" value="{{ old('address') }}" placeholder="Street address, city" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                </div>
                <div class="md:col-span-3 flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Add Customer
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-base">Customer Directory</h3>
            </div>
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-5 py-3 text-left">Customer</th>
                        <th class="px-5 py-3 text-left">Contact</th>
                        <th class="px-5 py-3 text-left">Membership Status</th>
                        <th class="px-5 py-3 text-right">Balance</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($customers as $customer)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-900">{{ $customer->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $customer->customer_id }}</div>
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                <div>{{ $customer->phone ?? '—' }}</div>
                                <div class="text-xs text-gray-400">{{ $customer->email ?? '—' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                @if ($customer->members->isNotEmpty())
                                    @php $activeMember = $customer->members->first(); @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                        Member: {{ $activeMember->member_number }} ({{ $activeMember->membershipType?->name ?? 'Standard' }})
                                    </span>
                                @else
                                    <a href="{{ route('members.index', ['customer_id' => $customer->id]) }}" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-200 hover:bg-indigo-100 transition">
                                        + Register as Member
                                    </a>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-gray-900">
                                ৳ {{ number_format($customer->current_balance, 2) }}
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Delete customer {{ $customer->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs text-red-600 hover:text-red-800 font-medium" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if ($customers->isEmpty())
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-gray-400">No customers registered yet.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($customers->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
