<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Units</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="POST" action="{{ route('units.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <input name="name" placeholder="Unit name" class="border rounded p-2" required>
                <input name="short_name" placeholder="Short name" class="border rounded p-2">
                <select name="status" class="border rounded p-2">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-3">Add unit</button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Short</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($units as $unit)
                        <tr>
                            <td class="px-4 py-3">{{ $unit->name }}</td>
                            <td class="px-4 py-3">{{ $unit->short_name }}</td>
                            <td class="px-4 py-3">{{ $unit->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
