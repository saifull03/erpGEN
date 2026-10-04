<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    protected $fillable = [
        'shift_number',
        'user_id',
        'opening_balance',
        'cash_sales',
        'cash_expenses',
        'cash_refunds',
        'cash_deposits',
        'cash_withdrawals',
        'expected_balance',
        'actual_balance',
        'difference',
        'status',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'cash_sales' => 'decimal:2',
        'cash_expenses' => 'decimal:2',
        'cash_refunds' => 'decimal:2',
        'cash_deposits' => 'decimal:2',
        'cash_withdrawals' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'actual_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'shift_id');
    }
}
