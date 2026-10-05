<x-guest-layout>
    <div class="mb-4">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center mx-auto mb-3 shadow-xs">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
        </div>
        <h2 class="text-center font-black text-xl text-gray-900 tracking-tight">Manager Authorization Required</h2>
        <p class="text-center text-xs text-gray-500 mt-1">This module is restricted. A Branch Store Manager must authorize this session.</p>
    </div>

    <!-- Active Cashier & Branch Context Card -->
    <div class="mb-5 p-3.5 bg-gray-50 rounded-xl border border-gray-200/80 text-xs space-y-1">
        <div class="flex justify-between">
            <span class="text-gray-400 font-bold uppercase text-[10px]">Logged-in Cashier:</span>
            <span class="font-black text-gray-900">{{ $user->name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-400 font-bold uppercase text-[10px]">Assigned Branch:</span>
            <span class="font-black text-indigo-600">{{ $branch?->name ?? 'Main Outlet' }} ({{ $branch?->code ?? 'HQ' }})</span>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-4 p-3 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-rose-800 text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div class="mb-4 p-3 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg text-amber-800 text-xs font-semibold">
            {{ session('info') }}
        </div>
    @endif

    <form method="POST" action="{{ route('manager.override.submit') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="intended_url" value="{{ $targetUrl }}">

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Branch Manager Email (Optional)</label>
            <input type="email" name="manager_email" placeholder="manager@erpgen.local" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-xs">
            <p class="text-[10px] text-gray-400 mt-0.5">Leave blank to match any active manager in {{ $branch?->name ?? 'this branch' }}.</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Branch Manager Password <span class="text-rose-500">*</span></label>
            <input type="password" name="manager_password" required placeholder="Enter manager password" autofocus class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-xs">
        </div>

        <div class="pt-2 flex flex-col gap-2">
            <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-xs transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Authorize & Continue
            </button>

            <a href="{{ route('pos.index') }}" class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl text-center transition">
                Return to POS Terminal
            </a>
        </div>
    </form>
</x-guest-layout>
