<?php

namespace App\Providers;

use App\Services\Reports\ConsolidatedReportsService;
use App\Services\Reports\Routes\ControlPointService;
use App\Services\Reports\Routes\OffRoadService;
use App\Services\Reports\Routes\SpeedingService;
use Illuminate\Support\ServiceProvider;

class ConsolidatedReportsServiceProvider extends ServiceProvider
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
        $this->app->bind(ConsolidatedReportsService::class, function () {
            return new ConsolidatedReportsService(new OffRoadService(), new SpeedingService(), new ControlPointService());
        });
    }
}
