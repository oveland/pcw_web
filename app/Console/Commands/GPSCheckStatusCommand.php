<?php

namespace App\Console\Commands;

use Carbon\Carbon;
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

        $now = Carbon::now();
        $dateNow = $now->toDateString();
        $timeNow = $now->toTimeString();

        $sql = "
          UPDATE markers
          SET status = 1, period = 0, update_from_check = TRUE 
          WHERE (current_timestamp - updated_at)::interval > '$gpsTimeForNOReportPowerOn'::interval 
          AND status <> 6
        ";
        DB::update($sql);
        $this->info($sql);

        $sql = "
          UPDATE markers 
          SET status = 1, period = 0, update_from_check = TRUE
          WHERE (current_timestamp - updated_at)::interval > '$gpsTimeForNOReportPowerOff'::interval 
          AND status = 6
        ";
        DB::update($sql);
        $this->info($sql);

        DB::select("SELECT auto_close_dispatch_register_with_invalid_gps(21)");
    }
}
