<?php

namespace App\Providers;

use App\Models\Company\Company;
use App\Services\DFS\DFSSyncService;
use App\Services\LM\LMRepository;
use App\Services\LM\LMService;
use App\Services\BEA\BEASyncService;
use App\Services\LM\CommissionService;
use App\Services\BEA\DBService as BEADatabase;
use App\Services\DFS\DBService as DFSDatabase;
use App\Services\LM\DiscountService;
use App\Services\LM\PenaltyService;
use App\Services\LM\Reports\LMReportService;
use Auth;
use Illuminate\Support\ServiceProvider;

class LMServiceProvider extends ServiceProvider
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
        $this->app->bind('lm.db.bea', function () {
            return new BEADatabase();
        });

        $this->app->bind('lm.db.dfs', function () {
            return new DFSDatabase();
        });

        $this->app->bind('lm.service', function ($app, $params) {
            $user = Auth::user();

            $company = !$user || $user->isAdmin() || isset($params['console']) ? Company::find($params['company']) : $user->company;
            $repository = new LMRepository($company);
            $report = new LMReportService();

            $syncService = null;
            switch ($company->id) {
                case Company::EXPRESO_PALMIRA:
                    $syncService = new DFSSyncService($company, $repository);
                    break;
                case Company::COODETRANS:
                    $syncService = new BEASyncService($company, $repository);
                    break;
            }

            return new LMService($syncService, $report, $repository, new DiscountService($repository), new CommissionService($repository), new PenaltyService($repository));
        });
    }
}
