<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Employee & User Management</h2>
                <p class="text-sm text-gray-500 mt-1">Manage supermarket staff members, assign system roles and branch outlet locations.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{
        editModalOpen: false,
        editUser: {
            id: null,
            name: '',
            email: '',
            phone: '',
            role_id: '',
            branch_id: '',
            status: 'active'
        },
        openEdit(user) {
            this.editUser = {
                id: user.id,
                name: user.name,
                email: user.email,
                phone: user.phone || '',
                role_id: user.role_id || '',
                branch_id: user.branch_id || '',
                status: user.status || 'active'
            };
            this.editModalOpen = true;
        }
    }">
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-emerald-800 font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Add Staff Member Form -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
            <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Add Staff Member
            </h3>
            <form method="POST" action="{{ route('users.store') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @csrf
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name') }}" placeholder="e.g. Shakib Al Hasan" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="staff@erpgen.local" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Phone Number</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="01700000000" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">System Role <span class="text-red-500">*</span></label>
                    <select name="role_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Assigned Branch <span class="text-indigo-600 font-bold">(Required for Cashier)</span></label>
                    <select name="branch_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">HQ / Central (All Branches)</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Password <span class="text-red-500">*</span></label>
                    <input name="password" type="password" placeholder="Minimum 6 characters" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Account Status</label>
                    <select name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="lg:col-span-1 flex items-end">
                    <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition flex items-center justify-center">
                        Add User
                    </button>
                </div>
            </form>
        </div>

        <!-- Staff List Table -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-base">OneStop Staff Directory</h3>
                <span class="text-xs text-gray-500">{{ $users->total() ?? count($users) }} accounts</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Staff Name</th>
                            <th class="px-5 py-3.5 text-left">Email / Username</th>
                            <th class="px-5 py-3.5 text-left">Branch</th>
                            <th class="px-5 py-3.5 text-left">Role & Access</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                    @if ($user->phone)
                                        <div class="text-[11px] text-gray-400 font-mono">{{ $user->phone }}</div>
                                    @endif
                                    @if ($user->id === auth()->id())
                                        <span class="inline-flex items-center text-[10px] text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded font-semibold">You</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-gray-600">
                                    {{ $user->email }}
                                </td>
                                <td class="px-5 py-4 text-xs font-semibold text-gray-700">
                                    {{ $user->branch?->name ?? 'HQ / Consolidated' }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                        {{ $user->role?->slug === 'super-admin' ? 'bg-purple-100 text-purple-800' :
                                           ($user->role?->slug === 'admin' ? 'bg-indigo-100 text-indigo-800' :
                                           ($user->role?->slug === 'cashier' ? 'bg-emerald-100 text-emerald-800' :
                                           ($user->role?->slug === 'inventory-manager' ? 'bg-amber-100 text-amber-800' :
                                           ($user->role?->slug === 'accountant' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')))) }}">
                                        {{ $user->role?->name ?? 'Staff' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Staff Button -->
                                        <button 
                                            type="button"
                                            @click="openEdit({{ json_encode($user) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-lg transition border border-indigo-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>

                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete user {{ $user->name }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-xs rounded-lg transition border border-red-200" type="submit">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-gray-400 italic px-2">Active</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-400">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Edit Staff Member Modal -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div @click="editModalOpen = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-xs transition-opacity"></div>

                <div class="inline-block bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-2xl border border-gray-100">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                                ✏️
                            </div>
                            <div>
                                <h3 class="text-base font-black text-gray-900">Edit Staff Member</h3>
                                <p class="text-xs text-gray-500">Update role, assigned store branch, status, or credentials.</p>
                            </div>
                        </div>
                        <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
                    </div>

                    <form :action="`/users/${editUser.id}`" method="POST" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name *</label>
                                <input name="name" x-model="editUser.name" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address *</label>
                                <input name="email" type="email" x-model="editUser.email" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone Number</label>
                                <input name="phone" x-model="editUser.phone" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="01700000000">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">System Role *</label>
                                <select name="role_id" x-model="editUser.role_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Assigned Store Branch</label>
                                <select name="branch_id" x-model="editUser.branch_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">HQ / Central (All Outlets)</option>
                                    @foreach ($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Account Status *</label>
                                <select name="status" x-model="editUser.status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">New Password (Optional)</label>
                            <input name="password" type="password" placeholder="Leave blank to keep current password unchanged" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-[11px] text-gray-400">Only enter if you wish to reset or change the user's password.</span>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end gap-2">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

