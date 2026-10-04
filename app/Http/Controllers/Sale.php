<?php

namespace App\Http\Controllers;

use App\Models\Sale as SaleModel;
use Illuminate\Http\Request;

class Sale extends Controller
{
    public function index()
    {
        return view('sales.index', ['sales' => SaleModel::query()->with(['customer', 'member', 'user'])->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => ['required', 'string', 'max:50', 'unique:sales,invoice_number'],
            'grand_total' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['due_amount'] = max(0, $validated['grand_total'] - $validated['paid_amount']);
        $validated['user_id'] = auth()->id();

        SaleModel::query()->create($validated);

        return redirect()->route('sales.index')->with('success', 'Sale recorded.');
    }
}
