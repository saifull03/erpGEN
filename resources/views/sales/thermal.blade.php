<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->invoice_number }} - OneStop POS</title>
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 8px 12px;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            background: #fff;
        }
        .paper-58mm body {
            width: 58mm;
            padding: 4px 6px;
            font-size: 10.5px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: 900; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        .divider-double { border-top: 2px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 11px; }
        .paper-58mm table { font-size: 9.5px; }
        th, td { padding: 2px 0; vertical-align: top; }
        .no-print { 
            margin-bottom: 12px; 
            padding: 10px;
            background: #f3f4f6;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .btn {
            display: inline-block;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-primary { background: #111827; color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #374151; margin-left: 6px; }
        .btn-toggle { background: #4f46e5; color: #fff; margin-left: 6px; }
        @media print {
            .no-print { display: none !important; }
            body { 
                margin: 0 !important; 
                padding: 4px 6px !important;
                width: 100% !important;
                max-width: 80mm;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center">
        <div style="margin-bottom: 8px; font-weight: bold; font-family: sans-serif; font-size: 13px;">
            Thermal Receipt Printer
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary">🖨️ Print Now (Ctrl+P)</button>
            <button onclick="togglePaperSize()" id="sizeBtn" class="btn btn-toggle">Paper: 80mm</button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
    </div>

    <!-- Store Header -->
    <div class="text-center">
        <div class="bold" style="font-size: 17px; letter-spacing: 0.5px;">ONESTOP SUPERMARKET</div>
        <div>{{ \App\Models\Setting::where('key', 'store_address')->value('value') ?? 'House #12, Road #5, Dhanmondi, Dhaka' }}</div>
        <div>Hotline: {{ \App\Models\Setting::where('key', 'store_phone')->value('value') ?? '+880 1700-000000' }}</div>
        <div>BIN / VAT Reg: {{ \App\Models\Setting::where('key', 'store_bin')->value('value') ?? 'BIN-123456789-001' }}</div>
    </div>

    <div class="divider-double"></div>

    <!-- Sale & Customer Metadata -->
    <div style="font-size: 11px;">
        <div style="display: flex; justify-content: space-between;">
            <span>Invoice: <strong class="bold">{{ $sale->invoice_number }}</strong></span>
            <span>POS #01</span>
        </div>
        <div>Date: {{ $sale->created_at->format('d/m/Y h:i A') }}</div>
        <div>Cashier: {{ $sale->user?->name ?? 'Cashier' }}</div>
        @if ($sale->member)
            <div class="divider"></div>
            <div>Member: <strong class="bold">{{ $sale->member->name }}</strong></div>
            <div>Card No: <strong class="bold">{{ $sale->member->member_number }}</strong></div>
            <div>Tier: {{ $sale->member->membershipType?->name ?? 'Regular' }} | Pts: {{ number_format($sale->member->points) }}</div>
        @elseif ($sale->customer)
            <div>Customer: {{ $sale->customer->name }} ({{ $sale->customer->phone ?? 'N/A' }})</div>
        @else
            <div>Customer: Walk-in Customer</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Line Items -->
    <table>
        <thead>
            <tr style="border-bottom: 1px dashed #000;">
                <th class="text-left" style="width: 45%;">Item</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 20%;">Price</th>
                <th class="text-right" style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td class="text-left">
                        {{ Str::limit($item->product?->name ?? 'Item', 18) }}
                        @if ($item->discount_amount > 0)
                            <div style="font-size: 9px; color: #333;">(Disc: -৳{{ number_format($item->discount_amount, 2) }})</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right bold">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Totals Breakdown -->
    <table>
        <tr>
            <td class="text-left">Item Subtotal:</td>
            <td class="text-right">৳ {{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        @if ($sale->discount_amount > 0)
            <tr>
                <td class="text-left">Special Discount:</td>
                <td class="text-right">- ৳ {{ number_format($sale->discount_amount, 2) }}</td>
            </tr>
        @endif
        @if ($sale->tax_amount > 0)
            <tr>
                <td class="text-left">Govt. VAT ({{ number_format($sale->tax_rate ?? 5, 0) }}%):</td>
                <td class="text-right">৳ {{ number_format($sale->tax_amount, 2) }}</td>
            </tr>
        @endif
        <tr class="bold" style="font-size: 14px; border-top: 1px solid #000; border-bottom: 1px solid #000;">
            <td class="text-left" style="padding: 4px 0;">NET PAYABLE:</td>
            <td class="text-right" style="padding: 4px 0;">৳ {{ number_format($sale->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left" style="padding-top: 4px;">Paid Amount ({{ ucfirst($sale->payment_method) }}):</td>
            <td class="text-right bold" style="padding-top: 4px;">৳ {{ number_format($sale->paid_amount, 2) }}</td>
        </tr>
        @if ($sale->change_amount > 0)
            <tr class="bold" style="font-size: 13px;">
                <td class="text-left">CHANGE RETURNED:</td>
                <td class="text-right">৳ {{ number_format($sale->change_amount, 2) }}</td>
            </tr>
        @endif
        @if ($sale->due_amount > 0)
            <tr class="bold" style="color: #000;">
                <td class="text-left">REMAINING DUE:</td>
                <td class="text-right">৳ {{ number_format($sale->due_amount, 2) }}</td>
            </tr>
        @endif
    </table>

    <div class="divider-double"></div>

    <!-- Footer & Return Policy -->
    <div class="text-center" style="font-size: 10.5px;">
        <p class="bold">{{ \App\Models\Setting::where('key', 'receipt_footer')->value('value') ?? 'THANK YOU FOR SHOPPING AT ONESTOP!' }}</p>
        <p style="margin-top: 3px;">Items can be returned/exchanged within 7 days with original receipt and intact barcode tags.</p>
        <div style="margin-top: 6px; font-family: monospace; letter-spacing: 2px;" class="bold">
            * {{ $sale->invoice_number }} *
        </div>
        <p style="margin-top: 4px; font-size: 9.5px; color: #444;">OneStop Supermarket ERP & POS</p>
    </div>

    <script>
        let is58mm = false;
        function togglePaperSize() {
            is58mm = !is58mm;
            document.documentElement.classList.toggle('paper-58mm', is58mm);
            document.getElementById('sizeBtn').innerText = is58mm ? 'Paper: 58mm' : 'Paper: 80mm';
        }
        // Auto print prompt
        window.addEventListener('load', () => {
            // Optional auto trigger print
            // window.print();
        });
    </script>
</body>
</html>
