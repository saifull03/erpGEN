<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category', 'name');
    }
}
