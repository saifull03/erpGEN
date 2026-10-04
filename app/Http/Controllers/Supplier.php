<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\Supplier as SupplierModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Supplier extends Controller
{
    public function index()
    {
        return view('suppliers.index', [
            'suppliers' => SupplierModel::query()->with('purchases')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        if (empty($request->input('supplier_id'))) {
            do {
                $id = 'SUP-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            } while (SupplierModel::query()->where('supplier_id', $id)->exists());
            $request->merge(['supplier_id' => $id]);
        }

        $validated = $request->validate([
            'supplier_id' => ['required', 'string', 'max:50', 'unique:suppliers,supplier_id'],
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string'],
        ]);

        SupplierModel::query()->create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function update(Request $request, SupplierModel $supplier)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'string', 'max:50', 'unique:suppliers,supplier_id,' . $supplier->id],
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(SupplierModel $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    public function ledger(SupplierModel $supplier)
    {
        $supplier->load('purchases');
        $entries = LedgerEntry::query()
            ->where('ledger_type', 'supplier')
            ->where('entity_id', $supplier->id)
            ->latest()
            ->paginate(25);

        return view('suppliers.ledger', [
            'supplier' => $supplier,
            'entries' => $entries,
        ]);
    }
}
