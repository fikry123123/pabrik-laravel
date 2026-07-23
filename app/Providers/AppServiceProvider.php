<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Paksa semua URL yang di-generate memakai skema https saat di production
        // (di belakang proxy Render). Mencegah warning "form not secure" karena
        // action form ter-render sebagai http://.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
