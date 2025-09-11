<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use App\Auth\Guards\JwtTenantGuard;


use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
        Auth::extend('jwt-tenant', function ($app, $name, array $config) {
            return new JwtTenantGuard($app['request']);
        });
    }
}
