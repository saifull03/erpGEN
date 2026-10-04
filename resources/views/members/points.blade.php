<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Loyalty Points History: {{ $member->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Card #: <span class="font-mono font-bold text-indigo-700">{{ $member->member_number }}</span> | Tier: {{ $member->membershipType?->name }}</p>
            </div>
            <a href="{{ route('members.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">
                Back to Members
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Balance Header -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase text-gray-400">Current Available Reward Points</span>
                <p class="text-3xl font-black text-amber-600 mt-1">★ {{ number_format($member->points) }} <span class="text-sm font-normal text-gray-500">Points</span></p>
            </div>
            <div class="text-xs text-gray-500 space-y-1">
                <div>Lifetime Spend: <span class="font-bold text-gray-800">৳ {{ number_format($member->total_purchase_amount, 2) }}</span></div>
                <div>Total Transactions: <span class="font-bold text-gray-800">{{ $member->total_transactions }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-800 text-sm">
                Point Transactions Audit Trail
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Date & Time</th>
                        <th class="px-5 py-3.5 text-center">Type</th>
                        <th class="px-5 py-3.5 text-center">Points</th>
                        <th class="px-5 py-3.5 text-left">Reference Invoice</th>
                        <th class="px-5 py-3.5 text-left">Notes</th>
                        <th class="px-5 py-3.5 text-left">Processed By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($pointLogs as $log)
                        <tr>
                            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $log->created_at->format('M d, Y • h:i A') }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold uppercase
                                    @if($log->type === 'earned') bg-emerald-100 text-emerald-800
                                    @elseif($log->type === 'redeemed') bg-blue-100 text-blue-800
                                    @elseif($log->type === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center font-black {{ $log->points > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $log->points > 0 ? '+' : '' }}{{ $log->points }}
                            </td>
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-indigo-700">{{ $log->reference ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-xs text-gray-600">{{ $log->notes }}</td>
                            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $log->user?->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No point transactions recorded for this member.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if ($pointLogs->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $pointLogs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
