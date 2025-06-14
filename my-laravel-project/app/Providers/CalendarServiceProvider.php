<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\Calendar;

class CalendarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('calendar', function () {
            return new Calendar();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
