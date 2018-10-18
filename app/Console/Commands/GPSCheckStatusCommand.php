<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GPSCheckStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the report for gps and and set the corresponding status';

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
        $gpsTimeForNOReportPowerOn = config('gps.gps_time_for_NO_report_power_ON');
        $gpsTimeForNOReportPowerOff = config('gps.gps_time_for_NO_report_power_OFF');

        /* CHECK STATUS GPS */

        DB::update("
          UPDATE markers
          SET status = 1, period = 0 
          WHERE ( fecha < current_date OR (fecha = current_date AND hora < (current_time - '$gpsTimeForNOReportPowerOn'::TIME) ) ) 
          AND status <> 6
        ");

        DB::update("
          UPDATE markers 
          SET status = 1, period = 0 
          WHERE ( fecha < current_date OR (fecha = current_date AND hora < (current_time - '$gpsTimeForNOReportPowerOff'::TIME) ) ) 
          AND status = 6
        ");

    }
}
