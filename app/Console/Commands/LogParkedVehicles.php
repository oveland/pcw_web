<?php

namespace App\Console\Commands;

use App\CurrentParkingReport;
use App\ParkingReport;
use DB;
use Illuminate\Console\Command;

class LogParkedVehicles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:parked';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers a report of vehicles parked with a time greater than a threshold value';

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
        $queryConditions = "m.status = 3 AND m.hora_status < (current_time - '00:01:00'::TIME)";
        $query = "
            SELECT
              current_timestamp date,
              v.id vehicle_id,
              m.lat latitude, m.lng longitude, m.orientacion orientation,
              cl.id location_id, cl.odometer, cl.speed,
              cr.id report_id, cr.dispatch_register_id, cr.distancem, cr.distanced, cr.distanced, cr.timem, cr.timep, cr.timed, cr.status_in_minutes, cr.control_point_id, cr.fringe_id
            FROM markers as m
              JOIN vehicles as v ON (v.plate = m.name)
              LEFT JOIN current_locations as cl ON (cl.vehicle_id = v.id)
              LEFT JOIN current_reports as cr ON (cr.vehicle_id = v.id)
            WHERE $queryConditions AND m.parked_reported IS FALSE
        ";
        $parkedVehicles = DB::select($query);

        if (count($parkedVehicles)) {
            //DB::statement("UPDATE markers SET parked_reported = TRUE, ignore_trigger = TRUE WHERE $queryConditions");
            DB::statement("UPDATE markers SET parked_reported = FALSE, ignore_trigger = TRUE");

            foreach ($parkedVehicles as $parkedVehicle) {
                $checked = true;
                $params = collect($parkedVehicle)->toArray();
                $currentParkingReport = CurrentParkingReport::findByVehicleId($parkedVehicle->vehicle_id)->first();

                if ($currentParkingReport) {
                    if ($currentParkingReport->latitude == $parkedVehicle->latitude && $currentParkingReport->longitude == $parkedVehicle->longitude) {
                        $this->info("The parked location for vehicle $parkedVehicle->vehicle_id is the same");
                        $checked = false;
                    }
                } else {
                    $currentParkingReport = new CurrentParkingReport();
                }

                if ($checked) {
                    $currentParkingReport->setRawAttributes($params);
                    $currentParkingReport->save();

                    $parkingReport = new ParkingReport();
                    $parkingReport->setRawAttributes($params);
                    $parkingReport->save();

                    $this->info("Vehicle parked ($parkedVehicle->date): $parkedVehicle->vehicle_id");
                }
            }
        } else {
            $this->info("There are not registers");
        }
    }
}
