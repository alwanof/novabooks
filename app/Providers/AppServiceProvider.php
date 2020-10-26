<?php

namespace App\Providers;

use App\Driver;
use App\Observers\DriverObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\UserObserver;
use App\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //User::observe(UserObserver::class);
        //Driver::observe(DriverObserver::class);
    }
}
