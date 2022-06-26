<?php

namespace App\Providers;

use App\Models\Company\Company;
use App\Services\LM\LMRepository;
use App\Services\LM\LMService;
use App\Services\BEA\BEASyncService;
use App\Services\LM\CommissionService;
use App\Services\LM\DiscountService;
use App\Services\LM\PenaltyService;
use App\Services\LM\Reports\LMReportService;
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
        $this->app->bind('bea.service', function ($app, $params) {
            $user = Auth::user();
            $dbId = $user ? $user->show_db_id : 1;

            $company = !$user || $user->isAdmin() || isset($params['console']) ? Company::find($params['company']) : $user->company;
            $repository = new LMRepository($company, $dbId);

            $report = new LMReportService($dbId);

            return new LMService(new BEASyncService($company, $repository), $report, $repository, new DiscountService($repository), new CommissionService($repository), new PenaltyService($repository), $dbId);
        });
    }
}
