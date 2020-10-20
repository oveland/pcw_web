<?php

namespace App\Console\Commands\BEA;

use App;
use App\Models\Company\Company;
use Exception;
use Illuminate\Console\Command;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bea:sync {--company=21}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync global params with BEA database';

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

        if ($company) {
            $beaService = App::makeWith('bea.service', ['company' => $company->id, 'console' => true]);

            $beaService->sync->routes();
            $beaService->sync->vehicles();
            $beaService->sync->drivers();

            $vehicles = $beaService->repository->getAllVehicles();

            $beaService->sync->discountTypes();
            foreach ($vehicles as $vehicle) {
                $beaService->sync->checkDiscountsFor($vehicle);
                $beaService->sync->checkPenaltiesFor($vehicle);
            }
            $beaService->sync->commissions();

        } else {
            $this->info('Company id not found');
        }

        return null;
    }
}
