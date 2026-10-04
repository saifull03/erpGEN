<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'module', 'record_id', 'old_value', 'new_value', 'ip_address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
