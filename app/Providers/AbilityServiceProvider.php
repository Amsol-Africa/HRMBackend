<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\SupportIssue;

class AbilityServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::define('markSolved', function ($user, SupportIssue $issue) {
            return $user->hasRole('business-admin') && $issue->business->users()->where('user_id', $user->id)->exists();
        });
    }
}