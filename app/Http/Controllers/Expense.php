<?php

namespace App\Http\Controllers;

use App\Models\Expense as ExpenseModel;
use Illuminate\Http\Request;

class Expense extends Controller
{
    public function index()
    {
        return view('expenses.index', ['expenses' => ExpenseModel::query()->with('createdBy')->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_id' => ['required', 'string', 'max:50', 'unique:expenses,expense_id'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['created_by'] = auth()->id();
        ExpenseModel::query()->create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }
}
