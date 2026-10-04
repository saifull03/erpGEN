<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Brands</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="POST" action="{{ route('brands.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input name="name" placeholder="Brand name" class="border rounded p-2" required>
                <select name="status" class="border rounded p-2">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-2">Add brand</button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Slug</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($brands as $brand)
                        <tr>
                            <td class="px-4 py-3">{{ $brand->name }}</td>
                            <td class="px-4 py-3">{{ $brand->slug }}</td>
                            <td class="px-4 py-3">{{ $brand->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
