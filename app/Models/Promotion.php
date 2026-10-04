<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'title',
        'code',
        'type',
        'value',
        'min_spend',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = now()->startOfDay();
        if ($this->start_date && $today->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $today->gt($this->end_date)) {
            return false;
        }

        return true;
    }
}
