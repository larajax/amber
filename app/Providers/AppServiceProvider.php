<?php

namespace App\Providers;

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
        // The app's own form widget, used in field definitions as type: timechecker
        app('system.widgets')->registerFormWidgets(function ($manager) {
            $manager->registerFormWidget(\App\FormWidgets\TimeChecker::class, 'timechecker');
        });
    }
}
