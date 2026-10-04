<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">System Audit Trail</h2>
                <p class="text-sm text-gray-500 mt-1">Immutable security and operations log tracking price updates, sales, returns, stock adjustments, and settings.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Filter Bar -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4">
            <form method="GET" action="{{ route('audit_logs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Module</label>
                    <select name="module" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Modules</option>
                        <option value="POS" {{ request('module') === 'POS' ? 'selected' : '' }}>POS / Sales</option>
                        <option value="Products" {{ request('module') === 'Products' ? 'selected' : '' }}>Products & Pricing</option>
                        <option value="Inventory" {{ request('module') === 'Inventory' ? 'selected' : '' }}>Inventory & Stock</option>
                        <option value="Purchases" {{ request('module') === 'Purchases' ? 'selected' : '' }}>Purchases</option>
                        <option value="Returns" {{ request('module') === 'Returns' ? 'selected' : '' }}>Returns</option>
                        <option value="Expenses" {{ request('module') === 'Expenses' ? 'selected' : '' }}>Expenses</option>
                        <option value="Users" {{ request('module') === 'Users' ? 'selected' : '' }}>Users & Staff</option>
                        <option value="Settings" {{ request('module') === 'Settings' ? 'selected' : '' }}>System Settings</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Search Keywords</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Record ID, IP, details" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Filter Audit Logs
                    </button>
                    @if (request()->hasAny(['module', 'search']))
                        <a href="{{ route('audit_logs.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Audit Logs Table -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-base">Activity Records</h3>
                <span class="text-xs text-gray-500">{{ $logs->total() ?? count($logs) }} events recorded</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Timestamp</th>
                            <th class="px-5 py-3.5 text-left">User / Cashier</th>
                            <th class="px-5 py-3.5 text-left">Module</th>
                            <th class="px-5 py-3.5 text-left">Action</th>
                            <th class="px-5 py-3.5 text-left">Record ID</th>
                            <th class="px-5 py-3.5 text-left">Details / Changes</th>
                            <th class="px-5 py-3.5 text-right">Client IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                    {{ $log->created_at ? $log->created_at->format('M d, Y - h:i:s A') : '—' }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-900 text-xs">
                                    {{ $log->user?->name ?? 'System' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $log->module }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs font-bold text-indigo-700">
                                    {{ $log->action }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-500">
                                    {{ $log->record_id ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-700 max-w-md break-all">
                                    @if ($log->new_value)
                                        <details class="cursor-pointer">
                                            <summary class="text-indigo-600 hover:underline font-mono">View payload</summary>
                                            <pre class="mt-1 p-2 bg-gray-900 text-emerald-400 rounded text-[11px] overflow-x-auto max-h-40">{{ is_array($log->new_value) ? json_encode($log->new_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $log->new_value }}</pre>
                                        </details>
                                    @elseif ($log->old_value)
                                        <span class="text-rose-600 font-mono">Deleted: {{ is_array($log->old_value) ? json_encode($log->old_value) : $log->old_value }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-gray-400">No audit trail records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
