<?php

namespace App\Http\Controllers;

use App\Models\Purchase as PurchaseModel;
use Illuminate\Http\Request;

class Purchase extends Controller
{
    public function index()
    {
        return view('purchases.index', ['purchases' => PurchaseModel::query()->with(['supplier', 'user'])->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_invoice_number' => ['required', 'string', 'max:50', 'unique:purchases,purchase_invoice_number'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'paid' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = auth()->id();
        $validated['due'] = max(0, $validated['total'] - $validated['paid']);
        PurchaseModel::query()->create($validated);

        return redirect()->route('purchases.index')->with('success', 'Purchase recorded.');
    }
}
