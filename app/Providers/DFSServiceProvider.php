<?php

namespace App\Providers;

use App\Models\Company\Company;
use App\Services\DFS\DFSSyncService;
use App\Services\LM\LMRepository;
use App\Services\LM\LMService;
use App\Services\BEA\BEASyncService;
use App\Services\LM\CommissionService;
use App\Services\LM\DiscountService;
use App\Services\LM\PenaltyService;
use App\Services\LM\Reports\LMReportService;
use Auth;
use Illuminate\Support\ServiceProvider;

class DFSServiceProvider extends ServiceProvider
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
        $this->app->bind('dfs.service', function ($app, $params) {
            $user = Auth::user();

            $company = !$user || $user->isAdmin() || isset($params['console']) ? Company::find($params['company']) : $user->company;
            $repository = new LMRepository($company);
            $report = new LMReportService();

            return new LMService(new DFSSyncService($company, $repository), $report, $repository, new DiscountService($repository), new CommissionService($repository), new PenaltyService($repository));
        });
    }
}
