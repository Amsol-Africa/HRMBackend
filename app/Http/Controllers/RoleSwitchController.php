<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    public function switchRole(Request $request)
    {
        $user = Auth::user();
        $newRole = $request->input('role');
        $business = Business::findBySlug(session('active_business_slug'));

        if (!$business) {
            return response()->json(['error' => 'No business context found'], 400);
        }

        if ($user->hasRole($newRole)) {
            session(['active_role' => $newRole]);
            $redirect = route($this->getRedirectRoute($newRole), $business->slug);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => $redirect]);
            }

            return redirect($redirect);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    private function getRedirectRoute($role)
    {
        return match ($role) {
            'business-admin' => 'business.index',
            'business-hr', 'business-finance' => 'business.index',
            'business-employee' => 'myaccount.index',
            default => 'dashboard',
        };
    }
}
