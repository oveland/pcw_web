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
        Log::useDailyFiles(storage_path().'/logs/gps-check-status.log',10);
        Log::info("---------------------------------------------------------");
        $gpsTimeForNOReportPowerOn = config('gps.gps_time_for_NO_report_power_ON');
        $gpsTimeForNOReportPowerOff = config('gps.gps_time_for_NO_report_power_OFF');

        /* CHECK STATUS GPS FOR COLOMBIA */
        DB::update("SET TIMEZONE = 'America/Bogota'");
        $queryForColombianGPSPowerON = "
          UPDATE markers
          SET status = 1, period = 0 
          WHERE ( fecha < current_date OR (fecha = current_date AND hora < (current_time - '$gpsTimeForNOReportPowerOn'::TIME) ) ) 
          AND status <> 6
        ";
        $queryForColombianGPSPowerON = DB::update($queryForColombianGPSPowerON);
        Log::info("TOTAL NO REPORT WITH POWER ON (COL) $queryForColombianGPSPowerON");

        $queryForColombianGPSPowerOFF = "
          UPDATE markers 
          SET status = 1, period = 0 
          WHERE ( fecha < current_date OR (fecha = current_date AND hora < (current_time - '$gpsTimeForNOReportPowerOff'::TIME) ) ) 
          AND status = 6
        ";
        $queryForColombianGPSPowerOFF = DB::update($queryForColombianGPSPowerOFF);
        Log::info("TOTAL NO REPORT WITH POWER OFF (COL) $queryForColombianGPSPowerOFF");

        /* CHECK STATUS GPS FOR NICARAGUA */
        /*DB::update("SET TIMEZONE = 'America/Managua'");
        $queryForNicaraguaGPSPowerON = "
          UPDATE markers 
          SET status = 1, period = 0 
          WHERE ( fecha < current_date OR (fecha = current_date AND hora < (current_time - '$gpsTimeForNOReportPowerOn'::TIME) ) ) 
          AND status <> 6 
          AND name IN (SELECT v.placa FROM crear_vehiculo as v WHERE v.empresa = 27)
        ";
        $queryForNicaraguaGPSPowerON = DB::update($queryForNicaraguaGPSPowerON);
        Log::info("TOTAL NO REPORT WITH POWER ON (NIC) $queryForNicaraguaGPSPowerON");

        $queryForNicaraguaGPSPowerOFF = "
          UPDATE markers 
          SET status = 1, period = 0 
          WHERE ( fecha < current_date OR (fecha = current_date AND hora < (current_time - '$gpsTimeForNOReportPowerOff'::TIME) ) ) 
          AND status = 6 
          AND name IN (SELECT v.placa FROM crear_vehiculo as v WHERE v.empresa = 27)
        ";
        $queryForNicaraguaGPSPowerOFF = DB::update($queryForNicaraguaGPSPowerOFF);
        Log::info("TOTAL NO REPORT WITH POWER OFF (NIC) $queryForNicaraguaGPSPowerOFF");*/

        /* Set default timezone for current session */
        DB::update("SET TIMEZONE = 'America/Bogota'");
    }
}
