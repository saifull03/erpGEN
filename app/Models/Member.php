<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'membership_id',
        'member_number',
        'customer_id',
        'membership_type_id',
        'name',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'join_date',
        'expiry_date',
        'points',
        'total_purchase_amount',
        'total_transactions',
        'status',
    ];

    protected $casts = [
        'points' => 'integer',
        'total_purchase_amount' => 'decimal:2',
        'total_transactions' => 'integer',
        'join_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(MembershipPointLog::class);
    }
}
