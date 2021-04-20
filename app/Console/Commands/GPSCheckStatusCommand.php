<?php

namespace App\Console\Commands;

use App\Models\Company\Company;
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

        $configCompanies = collect([
            36 => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            37 => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            26 => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::PCW => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::COOTRANSOL => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::ALAMEDA => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::SOTRAVALLE => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::MONTEBELLO => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::URBANUS_MONTEBELLO => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::TUPAL => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::YUMBENOS => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::COODETRANS => (object)[
                'timeOn' => $gpsTimeForNOReportPowerOn,
                'timeOff' => $gpsTimeForNOReportPowerOff,
            ],
            Company::BOOTHS => (object)[
                'timeOn' => '00:35:00',
                'timeOff' => '00:35:00',
            ]
        ]);

        /* CHECK STATUS GPS */
        foreach ($configCompanies as $companyId => $config) {
            $sql = "
                UPDATE markers
                SET status = 1, period = 0, update_from_check = TRUE
                WHERE (current_timestamp - updated_at)::interval > '$config->timeOn'::interval
                AND status <> 6 AND name IN (SELECT plate FROM vehicles WHERE company_id = $companyId)
            ";
            DB::update($sql);

            $sql = "
                UPDATE markers
                SET status = 1, period = 0, update_from_check = TRUE
                WHERE (current_timestamp - updated_at)::interval > '$config->timeOff'::interval
                AND status = 6 AND name IN (SELECT plate FROM vehicles WHERE company_id = $companyId)
            ";
            DB::update($sql);
        }

        DB::select("SELECT auto_close_dispatch_register_with_invalid_gps(21)");
    }
}
