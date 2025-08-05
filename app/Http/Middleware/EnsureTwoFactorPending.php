<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EnsureTwoFactorPending
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return Redirect::route('login')->withErrors(['email' => 'No two-factor authentication session found.']);
        }

        return $next($request);
    }
}
