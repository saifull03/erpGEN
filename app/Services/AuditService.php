<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log(
        string $action,
        string $module,
        ?string $recordId = null,
        mixed $oldValue = null,
        mixed $newValue = null,
        ?User $user = null
    ): AuditLog {
        $userId = $user ? $user->id : (auth()->id() ?? null);

        return AuditLog::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_value' => is_array($oldValue) || is_object($oldValue) ? json_encode($oldValue) : (string) $oldValue,
            'new_value' => is_array($newValue) || is_object($newValue) ? json_encode($newValue) : (string) $newValue,
            'ip_address' => Request::ip(),
        ]);
    }
}
