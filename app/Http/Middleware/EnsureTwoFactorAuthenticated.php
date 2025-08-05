<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // Skip middleware for 2FA routes
        if ($request->routeIs('2fa.verify') || $request->routeIs('2fa.resend')) {
            return $next($request);
        }

        // If user is in 2FA pending state, redirect to 2FA verification
        if ($request->session()->has('2fa_user_id')) {
            return redirect()->route('2fa.verify');
        }

        // If authenticated and 2FA is required but not verified, log out
        if (Auth::check() && Auth::user()->requiresTwoFactorAuthentication()) {
            if (!session('2fa_verified', false)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors(['email' => 'Two-factor authentication required.']);
            }
        }

        return $next($request);
    }
}
