<?php

namespace App\Http\Middleware;

use App\Models\LoginAttempt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $attempt = LoginAttempt::where('ip_address', $ip)->first();

        if ($attempt && $attempt->isBanned()) {
            return response()->json([
                'message' => 'Your IP is banned due to multiple failed login attempts. Try again later.'
            ], 403);
        }

        return $next($request);
    }
}
