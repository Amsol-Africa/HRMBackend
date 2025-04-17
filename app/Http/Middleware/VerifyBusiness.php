<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Business;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        $business = Business::findBySlug($request->route('business'));

        if (!$business) {
            return redirect()->route('dashboard')->with('error', 'Business not found.');
        }

        if (!$business->verified && $business->company_name !== 'amsol') {
            return redirect()->route('business.activate', $business->slug)
                ->with('message', 'Your business is not verified. Please contact Amsol support.');
        }

        return $next($request);
    }
}