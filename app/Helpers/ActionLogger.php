<?php

namespace App\Helpers;

use App\Models\ActionLogs;
use Illuminate\Support\Facades\Auth;

class ActionLogger
{
    public static function log($action)
    {
        if (Auth::check()) {
            ActionLogs::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'ip_address' => request()->ip(),
            ]);
        }
    }
}
