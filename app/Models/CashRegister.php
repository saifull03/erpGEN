<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegister extends Model
{
    protected $fillable = ['user_id', 'opening_balance', 'expected_balance', 'actual_balance', 'difference', 'status', 'closed_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
