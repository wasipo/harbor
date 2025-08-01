<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repository bindings are handled in AppServiceProvider
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
