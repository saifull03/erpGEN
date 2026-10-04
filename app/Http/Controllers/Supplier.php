<?php

namespace App\Http\Controllers;

use App\Models\Supplier as SupplierModel;
use Illuminate\Http\Request;

class Supplier extends Controller
{
    public function index()
    {
        return view('suppliers.index', ['suppliers' => SupplierModel::query()->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
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

        return redirect()->route('suppliers.index')->with('success', 'Supplier created.');
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

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(SupplierModel $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}
