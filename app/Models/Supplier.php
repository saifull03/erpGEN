<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_id',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'opening_balance',
        'current_payable_balance',
        'status',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
