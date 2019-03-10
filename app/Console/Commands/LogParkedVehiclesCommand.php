<?php

namespace App\Console\Commands;

use App\Models\Vehicles\CurrentParkingReport;
use App\Models\Vehicles\ParkingReport;
use DB;
use Illuminate\Console\Command;

class LogParkedVehiclesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:parked-vehicles';

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
        $timeParkedVehicleThreshold = config('road.time_parked_vehicle_threshold');

        $this->info("timeParkedVehicleThreshold = $timeParkedVehicleThreshold");
        $query = "
            SELECT
              current_timestamp date,
              v.id vehicle_id,
              cl.latitude, cl.longitude, cl.orientation,
              cl.id location_id, cl.odometer, cl.speed,
              cr.id report_id, cr.dispatch_register_id, cr.distancem, cr.distanced, cr.distanced, cr.timem, cr.timep, cr.timed, cr.status_in_minutes, cr.control_point_id, cr.fringe_id
            FROM markers as m
              JOIN vehicles as v ON (v.plate = m.name)
              LEFT JOIN current_locations as cl ON (cl.vehicle_id = v.id)
              LEFT JOIN current_reports as cr ON (cr.vehicle_id = v.id AND cr.date >= (current_date)::TIMESTAMP)
            WHERE m.status = 3 AND m.hora_status < (current_time - '$timeParkedVehicleThreshold'::TIME) AND m.parked_reported IS FALSE
        ";
        $parkedVehicles = DB::select($query);

        if (count($parkedVehicles)) {
            DB::statement("UPDATE markers SET parked_reported = TRUE, ignore_trigger = TRUE WHERE status = 3 AND hora_status < (current_time - '$timeParkedVehicleThreshold'::TIME)");

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
