<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\User;

class LedgerService
{
    public static function record(
        string $ledgerType,
        ?int $entityId,
        string $reference,
        string $description,
        float $debit = 0,
        float $credit = 0,
        ?User $user = null
    ): LedgerEntry {
        $userId = $user ? $user->id : (auth()->id() ?? null);

        // Calculate running balance for this specific entity/ledger_type
        $lastEntry = LedgerEntry::query()
            ->where('ledger_type', $ledgerType)
            ->where('entity_id', $entityId)
            ->latest('id')
            ->first();

        $previousBalance = $lastEntry ? (float) $lastEntry->balance : 0;
        $newBalance = round($previousBalance + $debit - $credit, 2);

        return LedgerEntry::query()->create([
            'date' => now()->toDateString(),
            'ledger_type' => $ledgerType,
            'entity_id' => $entityId,
            'reference' => $reference,
            'description' => $description,
            'debit' => round($debit, 2),
            'credit' => round($credit, 2),
            'balance' => $newBalance,
            'user_id' => $userId,
        ]);
    }
}
