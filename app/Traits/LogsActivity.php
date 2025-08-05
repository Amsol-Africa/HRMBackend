<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Http\RequestResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    public function logActivity($action)
    {
        $user = Auth::user();
        $modelName = class_basename($this);
        $title = $user ? "{$user->name} {$action} {$modelName}" : "System {$action} {$modelName}";
        $description = $user ? "{$user->name} has {$action} a {$modelName} record." : "System has {$action} a {$modelName} record.";

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'title' => $title,
            'description' => $description,
            'loggable_id' => $this->id,
            'loggable_type' => get_class($this),
        ]);
    }
}