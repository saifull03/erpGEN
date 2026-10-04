<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipType extends Model
{
    protected $fillable = ['name', 'discount_percentage', 'reward_points', 'minimum_purchase', 'is_active'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
