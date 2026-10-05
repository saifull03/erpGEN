<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight flex items-center gap-2.5">
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </span>
                    <span>Multi-Branch & Store Outlets</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Manage retail branches, regional store hubs, and assigned personnel</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('warehouses.index') }}" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl shadow-xs transition">
                    View Warehouses
                </a>
                <a href="{{ route('transfers.index') }}" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl shadow-xs transition">
                    Stock Transfers
                </a>
                <button @click="$dispatch('open-modal', 'add-branch-modal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add New Branch
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Branch Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($branches as $branch)
                    <div class="bg-white rounded-2xl border {{ $branch->is_main ? 'border-indigo-300 ring-2 ring-indigo-100 shadow-md' : 'border-gray-200 shadow-xs' }} p-5 flex flex-col justify-between relative overflow-hidden transition hover:shadow-md">
                        @if ($branch->is_main)
                            <div class="absolute top-0 right-0 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-wider px-3 py-0.5 rounded-bl-xl shadow-xs">
                                Head Office
                            </div>
                        @endif

                        <div>
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div>
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-black tracking-wider bg-gray-100 text-gray-700 mb-1">
                                        {{ $branch->code }}
                                    </span>
                                    <h3 class="font-black text-gray-900 text-base leading-tight">{{ $branch->name }}</h3>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black {{ $branch->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ ucfirst($branch->status) }}
                                </span>
                            </div>

                            <div class="space-y-1.5 text-xs text-gray-500 mb-4">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>{{ $branch->phone ?? 'No phone' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate">{{ $branch->address ?? 'No address set' }}</span>
                                </div>
                            </div>

                            <!-- Metrics Strip -->
                            <div class="grid grid-cols-3 gap-2 py-2.5 px-3 bg-gray-50 rounded-xl text-center border border-gray-100 mb-4">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Warehouses</p>
                                    <p class="text-sm font-black text-gray-800">{{ $branch->warehouses->count() }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Staff</p>
                                    <p class="text-sm font-black text-indigo-600">{{ $branch->users->count() }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Sales</p>
                                    <p class="text-sm font-black text-emerald-600">{{ $branch->sales_count }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                            <form action="{{ route('branches.switch') }}" method="POST">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                <button type="submit" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg transition">
                                    {{ session('active_branch_id') == $branch->id ? '✓ Current Branch' : 'Switch Context' }}
                                </button>
                            </form>

                            <button @click="$dispatch('open-modal', 'edit-branch-{{ $branch->id }}')" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Edit Branch Modal -->
                    <x-modal name="edit-branch-{{ $branch->id }}" focusable>
                        <form method="POST" action="{{ route('branches.update', $branch) }}" class="p-6">
                            @csrf
                            @method('PUT')
                            <h3 class="text-lg font-black text-gray-900 mb-4">Edit Branch - {{ $branch->name }}</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Branch Name</label>
                                    <input type="text" name="name" value="{{ old('name', $branch->name) }}" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Branch Code</label>
                                        <input type="text" name="code" value="{{ old('code', $branch->code) }}" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
                                        <select name="status" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="active" {{ $branch->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $branch->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" value="{{ old('email', $branch->email) }}" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Address</label>
                                    <textarea name="address" rows="2" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $branch->address) }}</textarea>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="is_main" id="is_main_{{ $branch->id }}" value="1" {{ $branch->is_main ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="is_main_{{ $branch->id }}" class="text-xs font-semibold text-gray-700">Set as Headquarters / Main Store</label>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </x-modal>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add Branch Modal -->
    <x-modal name="add-branch-modal" focusable>
        <form method="POST" action="{{ route('branches.store') }}" class="p-6">
            @csrf
            <h3 class="text-lg font-black text-gray-900 mb-4">Add New Retail Branch / Store</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Branch Name</label>
                    <input type="text" name="name" placeholder="e.g. Uttara Express Outlet" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Branch Code</label>
                        <input type="text" name="code" placeholder="e.g. BR-UTR05" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" placeholder="+8801700000000" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" placeholder="branch@onestop.local" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2" placeholder="Sector 3, Uttara, Dhaka" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_main" id="is_main_new" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_main_new" class="text-xs font-semibold text-gray-700">Set as Headquarters / Main Store</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">
                    Create Branch
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
