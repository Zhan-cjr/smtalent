<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public static function log(string $action, string $description, ?User $user = null, ?Request $request = null): AuditLog
    {
        $user = $user ?? Auth::user();
        $request = $request ?? request();

        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? substr($request->userAgent() ?? '', 0, 500) : null,
            'created_at' => now(),
        ]);
    }
}
