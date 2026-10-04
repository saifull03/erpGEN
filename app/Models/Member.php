<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }
}
