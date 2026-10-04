<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Product Brands</h2>
                <p class="text-sm text-gray-500 mt-1">Manage brand labels and product manufacturer associations.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-emerald-800 font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Add Brand Form -->
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 h-fit">
                <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create New Brand
                </h3>
                <form method="POST" action="{{ route('brands.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Brand Name <span class="text-red-500">*</span></label>
                        <input name="name" placeholder="e.g. Pran, Nestlé, Unilever, Aarong" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        Save Brand
                    </button>
                </form>
            </div>

            <!-- Right: Brand List Table -->
            <div class="lg:col-span-2 bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-base">Brand Directory</h3>
                    <span class="text-xs text-gray-500">{{ $brands->total() ?? count($brands) }} total brands</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                            <tr>
                                <th class="text-left px-5 py-3.5">Brand Name</th>
                                <th class="text-left px-5 py-3.5">Slug</th>
                                <th class="text-left px-5 py-3.5">Status</th>
                                <th class="text-right px-5 py-3.5">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($brands as $brand)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $brand->name }}</td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-gray-500">{{ $brand->slug }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $brand->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($brand->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <form method="POST" action="{{ route('brands.destroy', $brand) }}" onsubmit="return confirm('Delete brand {{ $brand->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:text-red-800 font-semibold" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-400">No brands found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($brands->hasPages())
                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                        {{ $brands->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
