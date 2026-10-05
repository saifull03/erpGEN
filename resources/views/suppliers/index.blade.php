<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Supplier Management</h2>
                <p class="text-sm text-gray-500 mt-1">Manage vendor contacts, purchase relationships, balances, and ledgers.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{
        editModalOpen: false,
        editSupplier: {
            id: null,
            supplier_id: '',
            company_name: '',
            contact_person: '',
            phone: '',
            email: '',
            address: '',
            status: 'active'
        },
        openEdit(s) {
            this.editSupplier = {
                id: s.id,
                supplier_id: s.supplier_id || '',
                company_name: s.company_name || '',
                contact_person: s.contact_person || '',
                phone: s.phone || '',
                email: s.email || '',
                address: s.address || '',
                status: s.status || 'active'
            };
            this.editModalOpen = true;
        }
    }">
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-emerald-800 font-medium text-sm">{{ session('success') }}</span>
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

        <!-- Add Supplier Form Card -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 mb-8">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Register New Supplier
            </h3>
            <form method="POST" action="{{ route('suppliers.store') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Supplier ID</label>
                    <input name="supplier_id" value="{{ old('supplier_id') }}" placeholder="Auto generated (e.g. SUP-0031)" class="w-full text-sm border-gray-300 rounded-lg shadow-sm font-mono focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Company Name <span class="text-red-500">*</span></label>
                    <input name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Meghna Group, Akij Food" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Contact Person</label>
                    <input name="contact_person" value="{{ old('contact_person') }}" placeholder="Representative Name" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Phone Number</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="e.g. 01700000000" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="vendor@example.com" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Opening Balance (৳)</label>
                    <input name="opening_balance" type="number" step="0.01" value="{{ old('opening_balance', '0.00') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Address / Warehouse</label>
                    <input name="address" value="{{ old('address') }}" placeholder="Office / Depo Address" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2 md:col-span-3 lg:col-span-4 flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Supplier
                    </button>
                </div>
            </form>
        </div>

        <!-- Supplier Directory Table -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-base">Suppliers Directory</h3>
                <span class="text-xs text-gray-500">{{ $suppliers->total() ?? count($suppliers) }} suppliers</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Supplier</th>
                            <th class="px-5 py-3.5 text-left">Contact Info</th>
                            <th class="px-5 py-3.5 text-left">Address</th>
                            <th class="px-5 py-3.5 text-right">Payable Balance</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($suppliers as $supplier)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900">{{ $supplier->company_name }}</div>
                                    <div class="text-xs text-indigo-600 font-mono font-medium">{{ $supplier->supplier_id }}</div>
                                    @if ($supplier->contact_person)
                                        <div class="text-xs text-gray-500 mt-0.5">Attn: {{ $supplier->contact_person }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-600">
                                    <div class="font-medium text-gray-800">{{ $supplier->phone ?? '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ $supplier->email ?? '—' }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600 max-w-xs truncate">
                                    {{ $supplier->address ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="text-sm font-extrabold {{ $supplier->current_payable_balance > 0 ? 'text-rose-600' : 'text-gray-900' }}">
                                        ৳ {{ number_format($supplier->current_payable_balance, 2) }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">Payable Due</div>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit Supplier -->
                                        <button 
                                            type="button"
                                            @click="openEdit({{ json_encode($supplier) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-200 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>

                                        <!-- Ledger -->
                                        <a href="{{ route('suppliers.ledger', $supplier) }}" class="inline-flex items-center px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg border border-gray-200 transition">
                                            Ledger
                                        </a>

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete supplier {{ $supplier->company_name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex items-center px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold rounded-lg border border-red-200 transition" type="submit">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-400">No suppliers registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($suppliers->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>

        <!-- Edit Supplier Modal -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div @click="editModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-xs transition-opacity"></div>

                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-xl border border-gray-100">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                                🏢
                            </div>
                            <div>
                                <h3 class="text-base font-black text-gray-900">Edit Supplier Details</h3>
                                <p class="text-xs text-gray-500">Update company name, representative contact, phone, and address.</p>
                            </div>
                        </div>
                        <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
                    </div>

                    <form :action="`/suppliers/${editSupplier.id}`" method="POST" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Supplier ID *</label>
                                <input name="supplier_id" x-model="editSupplier.supplier_id" class="w-full text-sm border-gray-300 rounded-lg font-mono bg-gray-50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Company Name *</label>
                                <input name="company_name" x-model="editSupplier.company_name" class="w-full text-sm border-gray-300 rounded-lg" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Contact Person</label>
                                <input name="contact_person" x-model="editSupplier.contact_person" class="w-full text-sm border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone Number</label>
                                <input name="phone" x-model="editSupplier.phone" class="w-full text-sm border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                                <input name="email" type="email" x-model="editSupplier.email" class="w-full text-sm border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status</label>
                                <select name="status" x-model="editSupplier.status" class="w-full text-sm border-gray-300 rounded-lg">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Address / Depo</label>
                            <input name="address" x-model="editSupplier.address" class="w-full text-sm border-gray-300 rounded-lg">
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end gap-2">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                Save Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

