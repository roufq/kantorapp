<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function record(string $action, $model = null, ?array $oldValues = null, ?array $newValues = null, ?array $meta = null): void
    {
        $auditableType = $model ? get_class($model) : null;
        $auditableId = $model && method_exists($model, 'getKey') ? $model->getKey() : null;
        $request = function_exists('request') ? request() : null;

        AuditLog::create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'meta' => $meta,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
