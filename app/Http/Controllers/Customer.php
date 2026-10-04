<?php

namespace App\Http\Controllers;

use App\Models\Customer as CustomerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Customer extends Controller
{
    public function index()
    {
        return view('customers.index', [
            'customers' => CustomerModel::query()->with('members.membershipType')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        if (empty($request->input('customer_id'))) {
            do {
                $generatedId = 'CUS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            } while (CustomerModel::query()->where('customer_id', $generatedId)->exists());
            $request->merge(['customer_id' => $generatedId]);
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'string', 'max:50', 'unique:customers,customer_id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string'],
        ]);

        CustomerModel::query()->create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function update(Request $request, CustomerModel $customer)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'string', 'max:50', 'unique:customers,customer_id,' . $customer->id],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(CustomerModel $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function ledger(CustomerModel $customer)
    {
        $customer->load('sales');
        $entries = \App\Models\LedgerEntry::query()
            ->where('ledger_type', 'customer')
            ->where('entity_id', $customer->id)
            ->latest()
            ->paginate(25);

        return view('customers.ledger', [
            'customer' => $customer,
            'entries' => $entries,
        ]);
    }
}
