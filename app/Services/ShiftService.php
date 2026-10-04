<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ShiftService
{
    public function getActiveShift(User $user): ?CashRegister
    {
        return CashRegister::query()
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();
    }

    public function openShift(User $user, float $openingBalance = 0, ?string $notes = null): CashRegister
    {
        $existing = $this->getActiveShift($user);
        if ($existing) {
            return $existing;
        }

        $shiftCount = CashRegister::query()->count() + 1;
        $shiftNumber = 'SHIFT-' . date('Ymd') . '-' . str_pad($shiftCount, 4, '0', STR_PAD_LEFT);

        $shift = CashRegister::query()->create([
            'shift_number' => $shiftNumber,
            'user_id' => $user->id,
            'opening_balance' => round($openingBalance, 2),
            'expected_balance' => round($openingBalance, 2),
            'actual_balance' => 0,
            'difference' => 0,
            'status' => 'open',
            'notes' => $notes,
            'opened_at' => now(),
        ]);

        AuditService::log('open_shift', 'Shift', (string) $shift->id, null, $shift->toArray(), $user);

        return $shift;
    }

    public function recordCashSales(CashRegister $shift, float $amount): void
    {
        $shift->cash_sales = round((float) $shift->cash_sales + $amount, 2);
        $shift->expected_balance = round(
            (float) $shift->opening_balance
            + (float) $shift->cash_sales
            + (float) $shift->cash_deposits
            - (float) $shift->cash_expenses
            - (float) $shift->cash_refunds
            - (float) $shift->cash_withdrawals,
            2
        );
        $shift->save();
    }

    public function recordCashRefund(CashRegister $shift, float $amount): void
    {
        $shift->cash_refunds = round((float) $shift->cash_refunds + $amount, 2);
        $shift->expected_balance = round(
            (float) $shift->opening_balance
            + (float) $shift->cash_sales
            + (float) $shift->cash_deposits
            - (float) $shift->cash_expenses
            - (float) $shift->cash_refunds
            - (float) $shift->cash_withdrawals,
            2
        );
        $shift->save();
    }

    public function recordCashExpense(CashRegister $shift, float $amount): void
    {
        $shift->cash_expenses = round((float) $shift->cash_expenses + $amount, 2);
        $shift->expected_balance = round(
            (float) $shift->opening_balance
            + (float) $shift->cash_sales
            + (float) $shift->cash_deposits
            - (float) $shift->cash_expenses
            - (float) $shift->cash_refunds
            - (float) $shift->cash_withdrawals,
            2
        );
        $shift->save();
    }

    public function closeShift(CashRegister $shift, float $actualBalance, ?string $notes = null, ?User $user = null): CashRegister
    {
        if ($shift->status === 'closed') {
            throw ValidationException::withMessages(['shift' => 'Shift is already closed.']);
        }

        $expected = (float) $shift->expected_balance;
        $actual = round($actualBalance, 2);
        $difference = round($actual - $expected, 2);

        $shift->actual_balance = $actual;
        $shift->difference = $difference;
        $shift->status = 'closed';
        $shift->closed_at = now();
        if ($notes) {
            $shift->notes = ($shift->notes ? $shift->notes . " | " : "") . $notes;
        }
        $shift->save();

        AuditService::log('close_shift', 'Shift', (string) $shift->id, ['expected' => $expected], ['actual' => $actual, 'diff' => $difference], $user);

        return $shift;
    }
}
