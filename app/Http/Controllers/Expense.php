<?php

namespace App\Http\Controllers;

use App\Models\Expense as ExpenseModel;
use App\Models\ExpenseCategory;
use App\Services\AuditService;
use App\Services\LedgerService;
use App\Services\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Expense extends Controller
{
    protected ShiftService $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function index(Request $request)
    {
        $query = ExpenseModel::query()->with('createdBy');

        if ($cat = $request->input('category')) {
            $query->where('category', $cat);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('date', '<=', $endDate);
        }

        $expenses = $query->latest()->paginate(20)->withQueryString();
        $categories = ExpenseCategory::all();

        return view('expenses.index', [
            'expenses' => $expenses,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        if (empty($request->input('expense_id'))) {
            do {
                $expId = 'EXP-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            } while (ExpenseModel::query()->where('expense_id', $expId)->exists());
            $request->merge(['expense_id' => $expId]);
        }

        $validated = $request->validate([
            'expense_id' => ['required', 'string', 'max:50', 'unique:expenses,expense_id'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'in:cash,card,bank_transfer,bkash,nagad'],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['payment_method'] = $validated['payment_method'] ?? 'cash';

        $expense = ExpenseModel::query()->create($validated);

        // If paid with cash, update active shift
        if ($validated['payment_method'] === 'cash') {
            $activeShift = $this->shiftService->getActiveShift($request->user());
            if ($activeShift) {
                $this->shiftService->recordCashExpense($activeShift, (float) $validated['amount']);
            }
        }

        // Ledger
        LedgerService::record(
            'expense',
            $expense->id,
            $expense->expense_id,
            "Expense [{$expense->category}]: {$expense->description}",
            (float) $expense->amount,
            0,
            $request->user()
        );

        AuditService::log('create_expense', 'Expenses', (string) $expense->id, null, $expense->toArray());

        return redirect()->route('expenses.index')->with('success', "Expense recorded successfully: ৳ {$expense->amount}");
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:expense_categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        ExpenseCategory::query()->create($validated);

        return redirect()->route('expenses.index')->with('success', "Expense category '{$validated['name']}' added.");
    }

    public function destroy(ExpenseModel $expense)
    {
        $id = $expense->expense_id;
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', "Expense {$id} deleted.");
    }
}
