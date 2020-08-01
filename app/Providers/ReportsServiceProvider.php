<?php

namespace App\Providers;

use App\Http\Controllers\GeneralController;
use App\Models\Company\Company;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Passengers\PassengersService;
use App\Services\Reports\Passengers\DetailedService;
use App\Services\Reports\Passengers\ConsolidatedService as ConsolidatedPassengersReports;


use App\Services\Reports\Routes\DispatchService;
use App\Services\Reports\Routes\RouteService;
use App\Services\Reports\Routes\OffRoadService;
use App\Services\Reports\Routes\SpeedingService;
use App\Services\Reports\Routes\ControlPointService;
use App\Services\Reports\Routes\ConsolidatedService as ConsolidatedRoutesReports;


use Auth;
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

        $this->app->bind(DispatchService::class, function ($app, $params) {
            $gc = new GeneralController();
            return new DispatchService($gc->getCompany(request()));
        });
    }
}
