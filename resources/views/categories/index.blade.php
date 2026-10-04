<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="POST" action="{{ route('categories.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input name="name" placeholder="Category name" class="border rounded p-2" required>
                <select name="status" class="border rounded p-2">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-2">Add category</button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3">Name</th>
                        <th class="text-left px-4 py-3">Slug</th>
                        <th class="text-left px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td class="px-4 py-3">{{ $category->name }}</td>
                            <td class="px-4 py-3">{{ $category->slug }}</td>
                            <td class="px-4 py-3">{{ $category->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
