<?php

namespace App\Providers;

use App\Services\Reports\Passengers\PassengersService;
use App\Services\Reports\Passengers\DetailedService;
use App\Services\Reports\Passengers\ConsolidatedService as ConsolidatedPassengersReports;


use App\Services\Reports\Routes\RouteService;
use App\Services\Reports\Routes\OffRoadService;
use App\Services\Reports\Routes\SpeedingService;
use App\Services\Reports\Routes\ControlPointService;
use App\Services\Reports\Routes\ConsolidatedService as ConsolidatedRoutesReports;


use Illuminate\Support\ServiceProvider;

class ReportsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PassengersService::class, function () {
            return new PassengersService(new ConsolidatedPassengersReports(), new DetailedService());
        });

        $this->app->bind(RouteService::class, function () {
            return new RouteService(new OffRoadService(), new ConsolidatedRoutesReports(new OffRoadService(), new SpeedingService(), new ControlPointService()));
        });
    }
}
