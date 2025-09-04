<?php

namespace App\Traits;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

trait LogsUserActivity
{
    /**
     * Log user activity
     */
    public static function logActivity(string $action, string $description, array $data = null, $userId = null)
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        
        if (!$user) {
            return;
        }

        $request = request();
        
        UserLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data' => $data
        ]);
    }
}
