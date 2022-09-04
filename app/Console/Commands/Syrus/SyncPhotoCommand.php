<?php

namespace App\Console\Commands\Syrus;

use App\Models\Company\Company;
use App\Services\GPS\Syrus\SyrusService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class SyncPhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syrus:sync-photos {--imei=357042066532541} {--company=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var SyrusService
     */
    private $syrusService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->syrusService = new SyrusService();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $imei = $this->option('imei');
        $company = $this->option('company');

        if ($company) {
            $company = Company::find($company);

            $vehicles = $company->activeVehicles;

            foreach ($vehicles as $vehicle) {
                $gps = $vehicle->gpsVehicle;
                if ($gps) {
                    $message = "Sync vehicle $vehicle->number imei $gps->imei";
                    dump($message);
                    $this->info($message);
                    $response = $this->syrusService->syncPhoto($gps->imei);
                    $this->info($response);
                }
            }
        } else if ($imei) {
            $response = $this->syrusService->syncPhoto($imei);
            $this->info($response);
        }
    }
}
