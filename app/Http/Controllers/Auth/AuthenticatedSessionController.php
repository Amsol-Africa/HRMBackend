<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemAlertNotification;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    use HandleTransactions;

    public function create(): View
    {
        $page = "Welcome Back!";
        $description = "Enter your credentials to log into your account";
        return view('auth.login', compact('page', 'description'));
    }

    public function store(LoginRequest $request, NotificationService $notificationService)
    {
        return $this->handleTransaction(function () use ($request, $notificationService) {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {

                $request->session()->regenerate();

                $user = Auth::user();
                $redirectUrl = $this->getRedirectUrlForRole($user);

                $channels = $notificationService->filterChannelsByUserPreferences($user->id, ['mail', 'database', 'slack']);

                $notificationService->sendNotification(
                    $user,
                    SystemAlertNotification::class,
                    ['System maintenance scheduled.', ['details' => 'Server will be down for 2 hours.']],
                    [],
                    $channels
                );

                return RequestResponse::ok('Welcome back.', ['redirect_url' => $redirectUrl]);
            }

            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        });
    }

    private function getRedirectUrlForRole($user)
    {
        //get users related business
        if ($user->hasRole('business-admin')) {

            $business = $user->business;
            session(['active_business_slug' => $business->slug]);
            session(['active_role' => 'business-admin']);

            if ($user->status === "setup") {
                return route('setup.business');
            } elseif ($user->status === "module") {
                return route('setup.modules');
            }

            return route('business.index', $business->slug);

        } elseif ($user->hasRole('business-hr')) {

            $business = $user->employee->business;
            session(['active_business_slug' => $business->slug]);
            session(['active_role' => 'business-hr']);
            return route('business.index', $business->slug);

        } elseif ($user->hasRole('business-finance')) {

            $business = $user->employee->business;
            session(['active_business_slug' => $business->slug]);
            session(['active_role' => 'business-finance']);
            return route('business.index', $business->slug);

        } elseif ($user->hasRole('business-employee')) {

            $business = $user->employee->business;
            session(['active_business_slug' => $business->slug]);
            session(['active_role' => 'business-employee']);

            return route('myaccount.index', $business->slug);

        }
    }

    public function destroy(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $name = explode(" ", $request->user()->name)[0];
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $redirect_url = route('login');

            return RequestResponse::ok('Come back soon ' . $name, ['redirect_url' => $redirect_url]);
        });
    }
}
