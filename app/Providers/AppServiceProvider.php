<?php


namespace App\Providers;

use App\Models\Business;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {

        });

        View::composer('*', function ($view) {
            $user = auth()->user();

            if ($user) {
                $businessSlug = session('active_business_slug');
                $business = $businessSlug ? Business::findBySlug($businessSlug) : $user->business;
                $view->with('currentBusiness', $business);
            } else {
                $view->with('currentBusiness', null);
            }
        });
    }
}
