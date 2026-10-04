<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use Illuminate\Http\Request;

class Account extends Controller
{
    public function ledger(Request $request)
    {
        $query = LedgerEntry::query()->with('user');

        if ($type = $request->input('ledger_type')) {
            $query->where('ledger_type', $type);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('date', '<=', $endDate);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $entries = $query->latest('date')->latest('id')->paginate(30)->withQueryString();

        $statsQuery = clone $query;
        $totalDebit = (float) $statsQuery->sum('debit');
        $totalCredit = (float) $statsQuery->sum('credit');

        return view('accounts.ledger', [
            'entries' => $entries,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
        ]);
    }
}
