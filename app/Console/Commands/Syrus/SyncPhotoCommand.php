<?php

namespace App\Console\Commands\Syrus;

use App\Models\Company\Company;
use App\Models\Vehicles\GpsVehicle;
use App\Services\GPS\Syrus\SyrusService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Log;

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
                    $this->log(" * $company->short_name | Start vehicle $vehicle->number imei $gps->imei");
//                    $response = $this->syrusService->syncPhoto($gps->imei);
//                    $this->info($response);
                    $this->log(" * $company->short_name | End vehicle $vehicle->number imei $gps->imei");
                }
            }
        } else if ($imei) {
            $gpsVehicle = GpsVehicle::where('imei', $imei)->first();
            if ($gpsVehicle && $gpsVehicle->vehicle) {
                $vehicle = $gpsVehicle->vehicle;
                $company = $vehicle->company;
                $this->log("$company->short_name | Start vehicle $vehicle->number($vehicle->id) imei $imei");
                $response = $this->syrusService->syncPhoto($imei);
                $this->log($response);
                $success = $response->get('success');
                $message = $response->get('message');
                $this->log("$company->short_name | End vehicle $vehicle->number($vehicle->id) imei $imei | Success: $success | $message");
            }
        }
    }

    function log($message)
    {
        $date = Carbon::now()->toDateTimeString();
        $this->info("$date | $message");
        Log::channel('rocket')->info("[Syrus3G sync] $message");
    }
}
