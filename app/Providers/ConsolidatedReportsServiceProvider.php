<?php

namespace App\Providers;

use App\Services\pcwserviciosgps\ConsolidatedReportsService;
use App\Services\pcwserviciosgps\reports\routes\ControlPointService;
use App\Services\pcwserviciosgps\reports\routes\OffRoadService;
use App\Services\pcwserviciosgps\reports\routes\SpeedingService;
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
