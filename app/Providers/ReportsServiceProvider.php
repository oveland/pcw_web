<?php

namespace App\Providers;

use App\Services\Exports\Routes\RouteExportService;
use App\Services\Reports\Passengers\PassengersService;
use App\Services\Reports\Passengers\ConsolidatedService as ConsolidatedPassengersReports;


use App\Services\Reports\Routes\DispatchRouteService;
use App\Services\Reports\Routes\RouteService;
use App\Services\Reports\Routes\OffRoadService;
use App\Services\Reports\Routes\SpeedingService;
use App\Services\Reports\Routes\ControlPointService;

use App\Services\Operation\Routes\Takings\RouteTakingsService;
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
            return new PassengersService(new ConsolidatedPassengersReports());
        });

        $this->app->bind(RouteService::class, function () {
            $dispatchService = new DispatchRouteService(new OffRoadService(), new SpeedingService(), new ControlPointService());

            return new RouteService($dispatchService, new RouteExportService(), new RouteTakingsService());
        });
    }
}
