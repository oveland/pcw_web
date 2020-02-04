<?php

namespace App\Providers;

use App\Models\Company\Company;
use App\Services\BEA\BEARepository;
use App\Services\BEA\BEAService;
use App\Services\BEA\BEASyncService;
use App\Services\BEA\CommissionService;
use App\Services\BEA\Database;
use App\Services\BEA\DiscountService;
use App\Services\BEA\PenaltyService;
use App\Services\BEA\Reports\BEAReportService;
use Auth;
use Illuminate\Support\ServiceProvider;

class BEAServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app->bind('bea.coodetrans', function () {
            return new Database(Company::COODETRANS);
        });

        $this->app->bind('bea.papagayo', function () {
            return new Database(Company::PAPAGAYO);
        });

        $this->app->bind('bea.service', function ($app, $params) {
            $user = Auth::user();

            $company = !$user  || $user->isAdmin() ? $params['company'] : $user->company;

            $repository = new BEARepository($company);
            $report = new BEAReportService();

            return new BEAService(new BEASyncService($company, $repository), $report, $repository, new DiscountService($repository), new CommissionService($repository), new PenaltyService($repository));
        });
    }
}
