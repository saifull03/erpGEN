<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'opening_balance',
        'current_balance',
        'status',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
