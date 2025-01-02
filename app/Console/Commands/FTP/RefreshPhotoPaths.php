<?php

namespace App\Console\Commands\FTP;

use App\Models\Vehicles\GpsVehicle;
use Illuminate\Console\Command;

class RefreshPhotoPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:refresh-photo-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $gpsVehicles = GpsVehicle::whereRaw('device_id IS NULL AND gps_type_id IS NOT NULL AND vehicle_id IN (SELECT id FROM vehicles WHERE company_id IN (2, 39, 41))')
            ->get();

        $gpsVehicles->pluck('imei')->each(function ($imei) {
           shell_exec("mkdir -p /home/pcwftp/syrus3G/$imei/images");
        });
    }
}
