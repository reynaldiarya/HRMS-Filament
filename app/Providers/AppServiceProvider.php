<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user) {
            return $user->hasRole('super_admin') ? true : null;
        });

        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            // Return the name of the policy class for the given model...
            return 'App\\Policies\\'.class_basename($modelClass).'Policy';
        });
    }
}
