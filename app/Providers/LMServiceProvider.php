<?php

namespace App\Providers;

use App\Models\Company\Company;
use App\Services\LM\Sources\Alameda\APIService;
use App\Services\LM\Sources\BEA\BEASyncService;
use App\Services\LM\Sources\DFS\DFSSyncService;
use App\Services\LM\Sources\EP\EPSyncService;
use App\Services\LM\LMRepository;
use App\Services\LM\LMService;
use App\Services\LM\Sources\Alameda\AlamedaSyncService;
use App\Services\LM\CommissionService;
use App\Services\LM\Sources\BEA\DBService as BEADatabase;
use App\Services\LM\Sources\DFS\DBService as DFSDatabase;
use App\Services\LM\Sources\EP\DBService as EPDatabase;
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

        $this->app->bind('ep.db', function () {
            return new EPDatabase();
        });

        $this->app->bind('lm.api.alameda', function () {
            return new APIService();
        });

        $this->app->bind('lm.service', function ($app, $params) {
            $user = Auth::user();
            $dbId = $user ? $user->show_db_id : 1;
            if (isset($params['db_id']) && $params['db_id']) {
                $dbId = $params['db_id'];
            }

            $company = !$user || $user->isAdmin() || isset($params['console']) ? Company::find($params['company']) : $user->company;
            $repository = new LMRepository($company, $dbId);
            $report = new LMReportService();

            $syncService = null;
            switch ($company->id) {
                case Company::EXPRESO_PALMIRA:
                    if($dbId == 1) $syncService = new EPSyncService($company, $repository);
                    else $syncService = new DFSSyncService($company, $repository);
                    break;
                case Company::COODETRANS:
                    $syncService = new BEASyncService($company, $repository);
                    break;
                case Company::ALAMEDA:
                    $syncService = new AlamedaSyncService($company, $repository);
                    break;
            }

            return new LMService($syncService, $report, $repository, new DiscountService($repository), new CommissionService($repository), new PenaltyService($repository), $dbId);
        });
    }
}
