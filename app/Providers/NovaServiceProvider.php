<?php

namespace App\Providers;

use App\Nova\Metrics\DriverCount;
use App\Nova\Metrics\DriverPartition;
use App\Nova\Metrics\DriverTrend;
use App\Nova\Metrics\OrderCount;
use App\Nova\Metrics\OrderTrend;
use App\Nova\Metrics\UserCount;
use App\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Silvanite\NovaToolPermissions\NovaToolPermissions;
use Digitalcloud\MultilingualNova\NovaLanguageTool;
use Muradalwan\DriversMap\DriversMap;
use Muradalwan\TaxiOrder\TaxiOrder;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        // Gate::define('viewNova', function ($user) {
        //     return in_array($user->email, [
        //         //
        //     ]);
        // });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            //new Help,
            (new DriversMap)->authUser(),
            new UserCount(),
            new DriverCount(),
            new DriverTrend(),
            new DriverPartition(),
            new OrderCount(),
            new OrderTrend()
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new NovaToolPermissions(),
            new NovaLanguageTool(),
            //(new TaxiOrder)->currentVisitors()
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
