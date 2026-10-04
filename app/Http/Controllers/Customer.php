<?php

namespace App\Http\Controllers;

use App\Models\Customer as CustomerModel;
use Illuminate\Http\Request;

class Customer extends Controller
{
    public function index()
    {
        return view('customers.index', ['customers' => CustomerModel::query()->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
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

        return redirect()->route('customers.index')->with('success', 'Customer created.');
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

        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(CustomerModel $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
