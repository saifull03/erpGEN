<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-900 tracking-tight flex items-center gap-2.5">
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    </span>
                    <span>Create Inter-Warehouse Stock Transfer</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Select dispatch origin, destination depot, and products to transfer</p>
            </div>
            <a href="{{ route('transfers.index') }}" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl shadow-xs transition">
                Back to Transfers
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('transfers.store') }}" x-data="{
                items: [
                    { product_id: '', quantity: 1 }
                ],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            }" class="space-y-6">
                @csrf

                <!-- Origin & Destination Info Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
                    <h3 class="font-black text-gray-900 text-base border-b border-gray-100 pb-3">Route Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Source / Dispatch Warehouse (From)</label>
                            <select name="from_warehouse_id" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select origin warehouse...</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->branch?->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Destination Warehouse (To)</label>
                            <select name="to_warehouse_id" required class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select receiving warehouse...</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->branch?->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Transfer Notes / Reason (Optional)</label>
                        <input type="text" name="notes" placeholder="e.g. Replenishment for weekend rush sale" class="w-full text-sm border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Transfer Items -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="font-black text-gray-900 text-base">Transfer Products</h3>
                        <button type="button" @click="addItem()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Another Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/70">
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Select Product</label>
                                    <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" required class="w-full text-xs border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Choose product...</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Total Stock: {{ $p->current_stock }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-32">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Quantity</label>
                                    <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity" min="1" required class="w-full text-xs font-bold border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div class="pt-5">
                                    <button type="button" @click="removeItem(index)" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('transfers.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        Create Requisition
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
