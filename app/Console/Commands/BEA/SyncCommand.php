<?php

namespace App\Console\Commands\BEA;

use App;
use App\Models\Company\Company;
use Exception;
use Illuminate\Console\Command;
use Log;

class SyncCommand extends Command
{

    /**
     * @var App\Services\BEA\BEASyncService
     */
    private $sync;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bea:sync {--company=21} {--type=all} {--db-id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync global params with LM database';

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
     * @throws Exception
     */
    public function handle()
    {
        $company = Company::find($this->option('company'));

        $this->info("LM sync for company: $company->name");

        $route = App\Models\Routes\Route::whereId(2);

        dd($route->toSql(), $route->getBindings());

        if ($company) {
            $beaService = App::makeWith('lm.service', ['company' => $company->id, 'console' => true]);

            $type = $this->option('type');

            $this->sync = $beaService->sync;
            $this->sync->for(null, null, $this->option('db-id'));

            if ($type === 'all') {
                $this->sync->routes();
                $this->sync->vehicles();
                $this->sync->drivers();

                $this->sync->trajectories();

                $vehicles = $beaService->repository->getAllVehicles();
                foreach ($vehicles as $vehicle) {
                    $this->sync->checkVehicleParams($vehicle);
                }
            } else {
                $this->sync->$type();
            }


        } else {
            $this->info('Company id not found');
        }

        return null;
    }

    /**
     * @param null $string
     * @param null $verbosity
     */
    public function info($string = null, $verbosity = null)
    {
        if ($string) {
            parent::info($string, $verbosity);
            Log::channel('lm')->info($string);
        }
    }
}
