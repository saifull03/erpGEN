<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $sale->invoice_number }} - OneStop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 p-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-start pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-black text-indigo-700">OneStop Supermarket</h1>
                <p class="text-sm text-gray-500 mt-1">{{ \App\Models\Setting::where('key', 'store_address')->value('value') ?? 'Dhanmondi, Dhaka, Bangladesh' }}</p>
                <p class="text-sm text-gray-500">Phone: {{ \App\Models\Setting::where('key', 'store_phone')->value('value') ?? '+880 1700-000000' }} | Email: {{ \App\Models\Setting::where('key', 'store_email')->value('value') ?? 'contact@onestop.local' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded uppercase mb-2">Retail Invoice</span>
                <p class="text-sm font-mono font-bold text-gray-800"># {{ $sale->invoice_number }}</p>
                <p class="text-xs text-gray-500 mt-1">Date: {{ $sale->created_at->format('M d, Y • h:i A') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 my-6 text-sm">
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase">Customer Information</h4>
                <p class="font-bold text-gray-800 text-base mt-1">{{ $sale->customer?->name ?? ($sale->member?->name ?? 'Walk-in Customer') }}</p>
                @if ($sale->customer?->phone || $sale->member?->phone)
                    <p class="text-gray-600">Phone: {{ $sale->customer?->phone ?? $sale->member?->phone }}</p>
                @endif
                @if ($sale->member)
                    <p class="text-indigo-600 font-semibold text-xs mt-1">Loyalty Member Card: {{ $sale->member->member_number }} (Tier: {{ $sale->member->membershipType?->name }})</p>
                @endif
            </div>
            <div class="text-right">
                <h4 class="text-xs font-bold text-gray-400 uppercase">Cashier & Shift</h4>
                <p class="font-semibold text-gray-800 mt-1">Served by: {{ $sale->user?->name ?? 'Cashier' }}</p>
                <p class="text-gray-500 text-xs">Payment Method: {{ ucfirst($sale->payment_method) }}</p>
                <p class="text-xs font-semibold text-emerald-600 uppercase mt-1">Status: {{ $sale->status }}</p>
            </div>
        </div>

        <table class="w-full text-sm border-collapse my-6">
            <thead>
                <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <th class="py-3 px-4 text-left">#</th>
                    <th class="py-3 px-4 text-left">Product Description</th>
                    <th class="py-3 px-4 text-center">Quantity</th>
                    <th class="py-3 px-4 text-right">Unit Price</th>
                    <th class="py-3 px-4 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($sale->items as $idx => $item)
                    <tr>
                        <td class="py-3 px-4 text-gray-500 text-xs">{{ $idx + 1 }}</td>
                        <td class="py-3 px-4 font-semibold text-gray-900">{{ $item->product?->name }}</td>
                        <td class="py-3 px-4 text-center font-bold">{{ $item->quantity }}</td>
                        <td class="py-3 px-4 text-right">৳ {{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3 px-4 text-right font-bold">৳ {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end my-6">
            <div class="w-64 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600"><span>Subtotal:</span><span>৳ {{ number_format($sale->subtotal, 2) }}</span></div>
                @if ($sale->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600"><span>Discount:</span><span>- ৳ {{ number_format($sale->discount_amount, 2) }}</span></div>
                @endif
                @if ($sale->tax_amount > 0)
                    <div class="flex justify-between text-gray-600"><span>VAT / Tax:</span><span>৳ {{ number_format($sale->tax_amount, 2) }}</span></div>
                @endif
                <div class="flex justify-between text-base font-black text-gray-900 pt-2 border-t border-gray-200">
                    <span>Grand Total:</span>
                    <span class="text-indigo-700">৳ {{ number_format($sale->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-bold"><span>Paid:</span><span>৳ {{ number_format($sale->paid_amount, 2) }}</span></div>
                @if ($sale->due_amount > 0)
                    <div class="flex justify-between text-red-600 font-bold"><span>Due:</span><span>৳ {{ number_format($sale->due_amount, 2) }}</span></div>
                @endif
            </div>
        </div>

        <div class="pt-6 border-t border-gray-200 text-center text-xs text-gray-400">
            <p>{{ \App\Models\Setting::where('key', 'receipt_footer')->value('value') ?? 'Thank you for shopping with OneStop Supermarket!' }}</p>
        </div>
    </div>
</body>
</html>
