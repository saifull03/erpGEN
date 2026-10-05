<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Card - {{ $member->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-6">
    <div class="no-print mb-6 flex gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-lg shadow">Print Membership Card</button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-gray-200 text-gray-700 font-bold text-sm rounded-lg">Close</button>
    </div>

    <!-- Printable PVC Plastic Card Format (85.60 × 53.98 mm ratio) -->
    <div class="w-[340px] h-[215px] bg-gradient-to-tr from-indigo-900 via-indigo-800 to-indigo-950 text-white rounded-2xl p-5 shadow-2xl relative overflow-hidden flex flex-col justify-between border border-indigo-700">
        <!-- Background decorative watermark -->
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>

        <!-- Header -->
        <div class="flex justify-between items-start z-10">
            <div>
                <span class="text-base font-black tracking-tight text-white flex items-center gap-1.5">
                    <span class="w-5 h-5 bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded font-black text-xs flex items-center justify-center">e</span>
                    erp<span class="text-indigo-400">GEN</span> Supermarket
                </span>
                <span class="text-[10px] text-indigo-300 uppercase tracking-widest block mt-0.5">Loyalty Privilege Club</span>
            </div>
            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                @if($member->membershipType?->name === 'Platinum') bg-purple-400 text-purple-950
                @elseif($member->membershipType?->name === 'Gold') bg-amber-400 text-amber-950
                @elseif($member->membershipType?->name === 'Silver') bg-slate-300 text-slate-900
                @else bg-blue-300 text-blue-950 @endif">
                {{ $member->membershipType?->name ?? 'Standard' }} Tier
            </span>
        </div>

        <!-- Member info -->
        <div class="z-10">
            <h2 class="text-base font-black text-white tracking-wide truncate">{{ $member->name }}</h2>
            <p class="text-xs text-indigo-200 font-mono font-bold tracking-widest mt-0.5">{{ $member->member_number }}</p>
            <p class="text-[10px] text-indigo-300">ID: {{ $member->membership_id }} • Points: {{ number_format($member->points) }}</p>
        </div>

        <!-- Footer & Barcode simulation -->
        <div class="flex justify-between items-end pt-2 border-t border-indigo-700/50 z-10">
            <div class="text-[9px] text-indigo-300 leading-tight">
                <div>Joined: {{ $member->join_date->format('M Y') }}</div>
                <div>{{ $member->expiry_date ? 'Exp: ' . $member->expiry_date->format('M Y') : 'Lifetime Member' }}</div>
            </div>

            <!-- SVG Barcode representation -->
            <div class="bg-white px-2 py-1 rounded">
                <svg class="h-6 w-24" viewBox="0 0 100 30">
                    <rect x="2" y="0" width="2" height="30" fill="#000"/>
                    <rect x="6" y="0" width="1" height="30" fill="#000"/>
                    <rect x="9" y="0" width="3" height="30" fill="#000"/>
                    <rect x="14" y="0" width="2" height="30" fill="#000"/>
                    <rect x="18" y="0" width="4" height="30" fill="#000"/>
                    <rect x="24" y="0" width="1" height="30" fill="#000"/>
                    <rect x="27" y="0" width="3" height="30" fill="#000"/>
                    <rect x="32" y="0" width="2" height="30" fill="#000"/>
                    <rect x="36" y="0" width="1" height="30" fill="#000"/>
                    <rect x="39" y="0" width="4" height="30" fill="#000"/>
                    <rect x="45" y="0" width="2" height="30" fill="#000"/>
                    <rect x="49" y="0" width="3" height="30" fill="#000"/>
                    <rect x="54" y="0" width="1" height="30" fill="#000"/>
                    <rect x="57" y="0" width="2" height="30" fill="#000"/>
                    <rect x="61" y="0" width="4" height="30" fill="#000"/>
                    <rect x="67" y="0" width="1" height="30" fill="#000"/>
                    <rect x="70" y="0" width="3" height="30" fill="#000"/>
                    <rect x="75" y="0" width="2" height="30" fill="#000"/>
                    <rect x="79" y="0" width="1" height="30" fill="#000"/>
                    <rect x="82" y="0" width="4" height="30" fill="#000"/>
                    <rect x="88" y="0" width="2" height="30" fill="#000"/>
                    <rect x="92" y="0" width="3" height="30" fill="#000"/>
                </svg>
            </div>
        </div>
    </div>
</body>
</html>
