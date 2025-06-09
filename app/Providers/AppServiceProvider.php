<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\BookingObserver;
use App\Models\Bookings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Bookings::observe(BookingObserver::class);
    }
}
