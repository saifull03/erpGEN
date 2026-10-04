<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Memberships</h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="POST" action="{{ route('members.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <input name="membership_id" placeholder="Membership ID" class="border rounded p-2" required>
                <input name="member_number" placeholder="Member number" class="border rounded p-2" required>
                <input name="name" placeholder="Member name" class="border rounded p-2" required>
                <input name="phone" placeholder="Phone" class="border rounded p-2">
                <input name="email" type="email" placeholder="Email" class="border rounded p-2">
                <input name="join_date" type="date" class="border rounded p-2">
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-3">Add member</button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Member</th>
                        <th class="px-4 py-3 text-left">Membership</th>
                        <th class="px-4 py-3 text-left">Phone</th>
                        <th class="px-4 py-3 text-left">Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $member)
                        <tr>
                            <td class="px-4 py-3">{{ $member->name }}</td>
                            <td class="px-4 py-3">{{ $member->membership_id }}</td>
                            <td class="px-4 py-3">{{ $member->phone }}</td>
                            <td class="px-4 py-3">{{ $member->points }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
