<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;

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

    // public function boot()
    // {
    //     // URL::forceRootUrl(request()->getSchemeAndHttpHost());
    //     // if ($this->app->environment('local')) {
    //     //     URL::forceRootUrl(config('app.url'));
    //     //     URL::forceScheme('https');
    //     //     config(['session.domain' => '.codjix.me']);
    //     // }

    // }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
            return null;
        });
    }
}
