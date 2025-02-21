<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
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

    public function store(LoginRequest $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {

                $user = Auth::user();
                $redirectUrl = $this->getRedirectUrlForRole($user);
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

            if($user->status === "setup") {
                return route('setup.business');
            }elseif($user->status === "module") {
                return route('setup.modules');
            }

            return route('business.index', $business->slug);

        } elseif($user->hasRole('business-hr')) {

            $business = $user->employee->business;
            session(['active_business_slug' => $business->slug]);
            return route('business.index', $business->slug);

        } elseif($user->hasRole('business-finance')) {

            $business = $user->employee->business;
            session(['active_business_slug' => $business->slug]);
            return route('business.index', $business->slug);

        } elseif($user->hasRole('business-employee')) {

            $business = $user->employee->business;
            session(['active_business_slug' => $business->slug]);
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
