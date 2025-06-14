<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Models\CompanyTheme;
use App\Models\Review;
use App\Observers\ReviewObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Calendar helper as a singleton
        $this->app->singleton('calendar', function () {
            return new \App\Helpers\Calendar();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix URL handling for asset storage in Windows environments
        \Illuminate\Support\Facades\URL::forceScheme('http');
        
        // Register Calendar facade
        $this->app->alias(\App\Helpers\Calendar::class, 'Calendar');
        $this->app->alias(\App\Facades\Calendar::class, 'Calendar');
        
        // Gebruik Bootstrap 5 voor paginering
        \Illuminate\Pagination\Paginator::useBootstrap();
        
        // Registreer observers
        Review::observe(ReviewObserver::class);
        
        // Standaard slijtage-instellingen toevoegen bij het maken van een advertentie
        \App\Models\Advertisement::creating(function ($advertisement) {
            if ($advertisement->is_rental && $advertisement->rental_calculate_wear_and_tear) {
                if (empty($advertisement->rental_wear_and_tear_settings)) {
                    $advertisement->rental_wear_and_tear_settings = [
                        'base_percentage' => 1.0, // 1% van de prijs per dag
                        'condition_multipliers' => [
                            'excellent' => 0.0,   // Perfect staat, geen slijtage
                            'good' => 0.5,        // Goede staat, halve slijtage
                            'fair' => 1.0,        // Normale slijtage
                            'poor' => 2.0,        // Slechte staat, dubbele slijtage
                        ]
                    ];
                }
            }
        });
        
        // Registreer policies
        \Illuminate\Support\Facades\Gate::define('return-orderItem', function ($user, $orderItem) {
            return $user->id === $orderItem->order->user_id;
        });
        
        // Advertisement beheer policy
        \Illuminate\Support\Facades\Gate::define('manage', function ($user, $advertisement) {
            return $user->id === $advertisement->user_id;
        });
        
        // Set the application locale from session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if ($locale === 'en' || $locale === 'nl') {
                App::setLocale($locale);
            }
        }
        
        // Theme configuration is now handled by ThemeServiceProvider
    }
}
