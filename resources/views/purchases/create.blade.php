<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Create Purchase Order</h2>
                <p class="text-sm text-gray-500 mt-0.5">Receive supplier shipments and update inventory automatically.</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">
                Back to Purchases
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{
        items: [
            { product_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 }
        ],
        discountAmount: 0,
        taxAmount: 0,
        paidAmount: 0,
        products: {{ json_encode($products) }},
        addItem() {
            this.items.push({ product_id: '', quantity: 1, unit_price: 0, discount: 0, tax: 0 });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        onProductChange(index) {
            let pId = this.items[index].product_id;
            let prd = this.products.find(p => p.id == pId);
            if (prd) {
                this.items[index].unit_price = prd.purchase_price;
            }
        },
        getItemSubtotal(item) {
            let qty = parseFloat(item.quantity) || 0;
            let price = parseFloat(item.unit_price) || 0;
            let disc = parseFloat(item.discount) || 0;
            let tax = parseFloat(item.tax) || 0;
            return Math.max(0, (qty * price) - disc + tax);
        },
        get subtotal() {
            return this.items.reduce((sum, item) => sum + this.getItemSubtotal(item), 0);
        },
        get total() {
            let disc = parseFloat(this.discountAmount) || 0;
            let tax = parseFloat(this.taxAmount) || 0;
            return Math.max(0, this.subtotal - disc + tax);
        },
        get due() {
            let paid = parseFloat(this.paidAmount) || 0;
            return Math.max(0, this.total - paid);
        }
    }">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-red-800 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('purchases.store') }}" class="space-y-6">
            @csrf

            <!-- Header info -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Supplier *</label>
                    <select name="supplier_id" class="w-full rounded-lg border-gray-300 text-sm" required>
                        <option value="">-- Choose Supplier --</option>
                        @foreach ($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->company_name }} ({{ $sup->supplier_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Purchase Invoice #</label>
                    <input name="purchase_invoice_number" placeholder="Auto generated PUR-..." class="w-full rounded-lg border-gray-300 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Date *</label>
                    <input name="date" type="date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status *</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm" required>
                        <option value="received">Received (Adds to Stock)</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Purchase Items</h3>
                    <button type="button" @click="addItem()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-200 transition">
                        + Add Item Row
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs">
                            <div class="sm:col-span-4">
                                <label class="block font-bold text-gray-600 mb-1">Product</label>
                                <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="onProductChange(index)" class="w-full text-xs rounded-md border-gray-300" required>
                                    <option value="">-- Choose Product --</option>
                                    <template x-for="p in products" :key="p.id">
                                        <option :value="p.id" x-text="p.name + ' (Stock: ' + p.current_stock + ')'"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-gray-600 mb-1">Qty</label>
                                <input :name="'items['+index+'][quantity]'" type="number" min="1" x-model="item.quantity" class="w-full text-xs font-bold text-center rounded-md border-gray-300" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-gray-600 mb-1">Unit Price (৳)</label>
                                <input :name="'items['+index+'][unit_price]'" type="number" step="0.01" min="0" x-model="item.unit_price" class="w-full text-xs font-bold text-right rounded-md border-gray-300" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-gray-600 mb-1">Discount (৳)</label>
                                <input :name="'items['+index+'][discount]'" type="number" step="0.01" min="0" x-model="item.discount" class="w-full text-xs text-right rounded-md border-gray-300">
                            </div>
                            <div class="sm:col-span-1 text-right">
                                <label class="block font-bold text-gray-600 mb-1">Subtotal</label>
                                <span class="font-black text-gray-900 text-xs" x-text="'৳ ' + getItemSubtotal(item).toFixed(2)"></span>
                            </div>
                            <div class="sm:col-span-1 text-center">
                                <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 font-bold p-1">&times;</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Totals & Payment Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-200">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Purchase Notes</label>
                        <textarea name="notes" rows="3" placeholder="Supplier delivery note, invoice details..." class="w-full text-sm rounded-lg border-gray-300"></textarea>
                    </div>
                    <div class="space-y-2 text-sm bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900">৳ <span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Discount (৳):</span>
                            <input name="discount_amount" type="number" step="0.01" min="0" x-model="discountAmount" class="w-24 text-right py-1 text-xs rounded border-gray-300 font-bold text-red-600">
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Tax / Freight (৳):</span>
                            <input name="tax_amount" type="number" step="0.01" min="0" x-model="taxAmount" class="w-24 text-right py-1 text-xs rounded border-gray-300 font-bold">
                        </div>
                        <div class="flex justify-between text-base font-black text-gray-900 pt-2 border-t border-gray-200">
                            <span>Total Order Amount:</span>
                            <span class="text-indigo-700">৳ <span x-text="total.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                            <span class="font-bold text-emerald-700">Amount Paid (৳):</span>
                            <input name="paid" type="number" step="0.01" min="0" x-model="paidAmount" class="w-28 text-right py-1 text-sm rounded border-gray-300 font-black text-emerald-700">
                        </div>
                        <div class="flex justify-between font-bold text-red-600">
                            <span>Remaining Due:</span>
                            <span>৳ <span x-text="due.toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                    Save Purchase Order
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
