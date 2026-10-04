<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode Label - {{ $product->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; background: white; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto">
        <div class="no-print mb-4 flex justify-between items-center">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-lg shadow">Print Labels</button>
            <button onclick="window.close()" class="px-3 py-2 bg-gray-200 text-gray-700 text-xs font-bold rounded-lg">Close</button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            @for ($i = 0; $i < 6; $i++)
                <div class="bg-white border border-gray-400 p-3 rounded text-center flex flex-col items-center justify-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-800">OneStop Supermarket</span>
                    <span class="text-xs font-bold text-gray-900 mt-1 truncate w-full">{{ $product->name }}</span>
                    
                    <!-- SVG Barcode visual -->
                    <div class="my-1.5 flex items-center justify-center">
                        <svg class="h-10 w-36" viewBox="0 0 140 40">
                            <!-- SVG lines simulation for standard 1D barcode -->
                            <rect x="5" y="0" width="2" height="35" fill="#000"/>
                            <rect x="9" y="0" width="1" height="35" fill="#000"/>
                            <rect x="12" y="0" width="3" height="35" fill="#000"/>
                            <rect x="18" y="0" width="1" height="35" fill="#000"/>
                            <rect x="22" y="0" width="2" height="35" fill="#000"/>
                            <rect x="27" y="0" width="4" height="35" fill="#000"/>
                            <rect x="34" y="0" width="2" height="35" fill="#000"/>
                            <rect x="38" y="0" width="1" height="35" fill="#000"/>
                            <rect x="42" y="0" width="3" height="35" fill="#000"/>
                            <rect x="48" y="0" width="2" height="35" fill="#000"/>
                            <rect x="53" y="0" width="1" height="35" fill="#000"/>
                            <rect x="57" y="0" width="3" height="35" fill="#000"/>
                            <rect x="63" y="0" width="2" height="35" fill="#000"/>
                            <rect x="68" y="0" width="4" height="35" fill="#000"/>
                            <rect x="75" y="0" width="1" height="35" fill="#000"/>
                            <rect x="79" y="0" width="3" height="35" fill="#000"/>
                            <rect x="85" y="0" width="2" height="35" fill="#000"/>
                            <rect x="90" y="0" width="1" height="35" fill="#000"/>
                            <rect x="94" y="0" width="4" height="35" fill="#000"/>
                            <rect x="101" y="0" width="2" height="35" fill="#000"/>
                            <rect x="106" y="0" width="3" height="35" fill="#000"/>
                            <rect x="112" y="0" width="1" height="35" fill="#000"/>
                            <rect x="116" y="0" width="2" height="35" fill="#000"/>
                            <rect x="121" y="0" width="3" height="35" fill="#000"/>
                            <rect x="127" y="0" width="2" height="35" fill="#000"/>
                            <rect x="132" y="0" width="1" height="35" fill="#000"/>
                        </svg>
                    </div>

                    <span class="font-mono text-[11px] font-black text-gray-800 tracking-widest">{{ $product->barcode ?? $product->sku }}</span>
                    <span class="text-sm font-black text-indigo-800 mt-1">Price: ৳ {{ number_format($product->selling_price, 2) }}</span>
                </div>
            @endfor
        </div>
    </div>
</body>
</html>
