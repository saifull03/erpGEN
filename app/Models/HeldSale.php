<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeldSale extends Model
{
    protected $fillable = [
        'reference_code',
        'cashier_id',
        'customer_id',
        'member_id',
        'member_number',
        'cart_data',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'cart_data' => 'array',
        'subtotal' => 'decimal:2',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
