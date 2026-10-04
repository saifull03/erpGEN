<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\User;
use App\Services\ShiftService;
use Illuminate\Http\Request;

class Shift extends Controller
{
    protected ShiftService $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function index(Request $request)
    {
        $query = CashRegister::query()->with('user');

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $shifts = $query->latest()->paginate(20)->withQueryString();
        $users = User::query()->where('status', 'active')->get();
        $activeShift = $this->shiftService->getActiveShift($request->user());

        return view('shifts.index', [
            'shifts' => $shifts,
            'users' => $users,
            'activeShift' => $activeShift,
        ]);
    }

    public function open(Request $request)
    {
        $request->validate([
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->shiftService->openShift(
            $request->user(),
            (float) $request->input('opening_balance'),
            $request->input('notes')
        );

        return redirect()->back()->with('success', 'Shift opened successfully.');
    }

    public function close(Request $request, CashRegister $shift)
    {
        $request->validate([
            'actual_balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->shiftService->closeShift(
            $shift,
            (float) $request->input('actual_balance'),
            $request->input('notes'),
            $request->user()
        );

        return redirect()->back()->with('success', "Shift closed. Difference: ৳ " . number_format($shift->difference, 2));
    }
}
