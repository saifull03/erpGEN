<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('settings.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input name="shop_name" value="{{ old('shop_name', $settings['shop_name'] ?? 'OneStop') }}" placeholder="Shop name" class="border rounded p-2" required>
                <input name="currency" value="{{ old('currency', $settings['currency'] ?? 'BDT') }}" placeholder="Currency" class="border rounded p-2" required>
                <input name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" placeholder="Phone" class="border rounded p-2">
                <input name="email" value="{{ old('email', $settings['email'] ?? '') }}" type="email" placeholder="Email" class="border rounded p-2">
                <textarea name="address" rows="3" placeholder="Address" class="border rounded p-2 md:col-span-2">{{ old('address', $settings['address'] ?? '') }}</textarea>
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 md:col-span-2">Save settings</button>
            </form>
        </div>
    </div>
</x-app-layout>
