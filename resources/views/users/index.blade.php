<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Employee & User Management</h2>
                <p class="text-sm text-gray-500 mt-1">Manage supermarket staff members, assign system roles (Super Admin, Manager, Cashier, Inventory, Accountant).</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
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
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="staff@onestop.local" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
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
                <div class="lg:col-span-3 flex items-end">
                    <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Create User Account
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
                            <th class="px-5 py-3.5 text-left">Phone</th>
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
                                    @if ($user->id === auth()->id())
                                        <span class="inline-flex items-center text-[10px] text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded font-semibold">You</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-gray-600">
                                    {{ $user->email }}
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">
                                    {{ $user->phone ?? '—' }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                        {{ $user->role?->slug === 'super-admin' ? 'bg-purple-100 text-purple-800' :
                                           ($user->role?->slug === 'cashier' ? 'bg-emerald-100 text-emerald-800' :
                                           ($user->role?->slug === 'inventory-manager' ? 'bg-amber-100 text-amber-800' :
                                           ($user->role?->slug === 'accountant' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ $user->role?->name ?? 'Staff' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete user {{ $user->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:text-red-800 font-semibold" type="submit">Delete</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Current Session</span>
                                    @endif
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
    </div>
</x-app-layout>
