<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        // Set the application locale from session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if ($locale === 'en' || $locale === 'nl') {
                App::setLocale($locale);
            }
        }
    }
}
