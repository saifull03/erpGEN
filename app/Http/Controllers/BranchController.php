<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::with(['warehouses', 'users'])->withCount(['sales', 'purchases', 'expenses'])->get();
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_main' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['is_main'] = $request->boolean('is_main');

        if ($validated['is_main']) {
            Branch::query()->update(['is_main' => false]);
        }

        $branch = Branch::create($validated);

        // Auto-create a primary warehouse for the new branch
        Warehouse::create([
            'branch_id' => $branch->id,
            'name' => "{$branch->name} Main Storage",
            'code' => 'WH-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $branch->code), 0, 6)) . '-01',
            'phone' => $branch->phone,
            'address' => $branch->address,
            'is_primary' => true,
            'status' => 'active',
        ]);

        return redirect()->route('branches.index')->with('success', 'Branch and default primary warehouse created successfully.');
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_main' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['is_main'] = $request->boolean('is_main');

        if ($validated['is_main'] && !$branch->is_main) {
            Branch::query()->where('id', '!=', $branch->id)->update(['is_main' => false]);
        }

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        if ($branch->is_main) {
            return back()->with('error', 'Cannot delete the main headquarters branch.');
        }

        if ($branch->sales()->exists() || $branch->purchases()->exists()) {
            return back()->with('error', 'Cannot delete a branch with historical sales or purchase records.');
        }

        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }

    public function switchBranch(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->role?->slug === 'cashier') {
            return back()->with('error', 'Cashiers are restricted to their assigned branch.');
        }

        $branchId = $request->input('branch_id');

        if ($branchId === 'all' || empty($branchId)) {
            session()->forget('active_branch_id');
            session()->forget('active_branch_name');
        } else {
            $branch = Branch::findOrFail($branchId);
            session(['active_branch_id' => $branch->id]);
            session(['active_branch_name' => $branch->name]);
        }

        return back()->with('success', 'Active branch context switched.');
    }
}
