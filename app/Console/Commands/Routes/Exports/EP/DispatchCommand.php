<?php

namespace App\Console\Commands\Routes\Exports\EP;

use App\Models\Company\Company;
use App\Models\Users\User;
use App\Services\Exports\Routes\RouteExportEPService;
use App\Services\Reports\Routes\DispatchRouteService;
use App\Services\Reports\Routes\RouteService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Auth;

class DispatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes:export:ep:dispatch {--from=} {--to=} {--company-id=39}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export excel report for dispatch registers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $from = $this->option('from') ?: Carbon::now()->toDateString();
        $to = $this->option('to') ?: Carbon::now()->toDateString();
        $company = Company::find($this->option('company-id'));

        $start = Carbon::now();
        $this->log("Start export EP Dispatch Excel Report");

        Auth::login(User::find(2018101392)); // log as PCW BOOT

        $dispatchRouteService = new DispatchRouteService(null, null, null);
        $drByVehicles = $dispatchRouteService->allByVehicles($company, $from, $to);

        $routeExportEPService = new RouteExportEPService();

        $filePath = $routeExportEPService->groupedRouteReport($drByVehicles, $from, $to, true);

        shell_exec("cp $filePath /var/www/files/exports");

        $fileName = trim(collect(explode('/', $filePath))->last());

        $link = "https://pcwserviciosgps.com/files/exports/$fileName";

        dump($link);

        $duration = Carbon::now()->diffInMinutes($start);
        $this->log("Ends export EP Dispatch Excel Report. In $duration minutes");
    }

    public function log($message)
    {
        $message = Carbon::now()->toDateTimeString() . " • $message";
        \Log::info($message);
        $this->info($message);
    }
}
