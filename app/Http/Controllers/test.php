<!-- <?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    public function switchRole(Request $request)
    {
        Log::info('Reached switchRole method', ['request' => $request->all()]);

        $user = Auth::user();
        $newRole = $request->input('role');

        // Log request and session data
        Log::info('Switch role request:', $request->all());
        Log::info('User roles:', $user->roles->pluck('name')->toArray());
        Log::info('Session values:', session()->all());

        // Validate role input
        if (!$newRole) {
            Log::error('No role specified');
            return response()->json(['error' => 'No role specified'], 400);
        }

        // Check business context
        $slug = session('active_business_slug');
        $business = Business::where('slug', $slug)->first();

        if (!$business) {
            Log::error('No business context found for slug: ' . $slug);
            return response()->json(['error' => 'No business context found'], 400);
        }

        // Check if user has the role
        if ($user->hasRole($newRole)) {
            session(['active_role' => $newRole]);
            $redirect = route($this->getRedirectRoute($newRole), $business->slug);
            Log::info('Role switched successfully to: ' . $newRole, ['redirect' => $redirect]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => $redirect]);
            }

            return redirect($redirect);
        }

        Log::warning('Unauthorized role switch attempt', ['user_id' => $user->id, 'role' => $newRole]);
        return response()->json(['error' => 'You do not have permission to switch to this role'], 403);
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
} -->


// ensure role

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureCorrectRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $activeRole = session('active_role');

        // Log for debugging
        Log::info('EnsureCorrectRole Middleware', [
            'user_id' => $user?->id,
            'active_role' => $activeRole,
            'active_role_type' => gettype($activeRole),
            'route' => $request->path(),
            'user_roles' => $user ? $user->getRoleNames() : null,
        ]);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized: No authenticated user'], 403);
        }

        // Set default role if none exists and user has business-admin
        if (!$activeRole && $user->hasRole('business-admin')) {
            $activeRole = 'business-admin';
            session(['active_role' => $activeRole]);
            Log::info('Set default active_role to business-admin', ['user_id' => $user->id]);
        }

        if (!$activeRole || !is_string($activeRole)) {
            return response()->json(['message' => 'Unauthorized: Invalid or missing role'], 403);
        }

        if (!$user->hasRole($activeRole)) {
            return response()->json(['message' => 'Unauthorized: User does not have the required role'], 403);
        }

        return $next($request);
    }
}
