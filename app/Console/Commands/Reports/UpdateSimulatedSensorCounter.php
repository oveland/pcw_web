<?php

namespace App\Console\Commands\Reports;

use App\Models\Routes\DispatchRegister;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSimulatedSensorCounter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:update-simulated-sensor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update final_sensor_counter with simulated data based on spreadsheet_passengers_sync for non-5G vehicles';

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
        $this->info('Starting simulated sensor counter update...');

        $routes = [285, 286, 287, 288, 321, 322, 331, 332, 337, 338, 341, 342, 343, 344, 347, 348];
        $specialRoutes = [279, 280, 282, 283];
        $specialVehicles = [2590, 2637];

        $tenDaysAgo = Carbon::now()->subDays(10)->startOfDay();

        // Target dispatch registers
        $dispatchRegisters = DispatchRegister::where(function ($query) use ($routes, $specialRoutes, $specialVehicles) {
                $query->whereIn('route_id', $routes)
                      ->orWhere(function ($q) use ($specialRoutes, $specialVehicles) {
                          $q->whereIn('route_id', $specialRoutes)
                            ->whereIn('vehicle_id', $specialVehicles);
                      });
            })
            ->where('date', '>=', $tenDaysAgo)
            ->where(function ($query) {
                $query->whereNull('final_sensor_counter')
                      ->orWhere('final_sensor_counter', 0);
            })
            ->with(['drObservations', 'vehicle', 'vehicle.gpsVehicle'])
            ->get();

        $this->info("Total dispatch registers found in DB: " . $dispatchRegisters->count());
        $specialCount = $dispatchRegisters->filter(function($dr) use ($specialRoutes, $specialVehicles) {
            return in_array($dr->route_id, $specialRoutes) && in_array($dr->vehicle_id, $specialVehicles);
        })->count();
        $this->info("Of those, special vehicles/routes found: " . $specialCount);

        // Filter non-5G vehicles manually
        $dispatchRegisters = $dispatchRegisters->filter(function ($dispatchRegister) {
            $vehicle = $dispatchRegister->vehicle;
            if (!$vehicle) return false;
            
            $gpsVehicle = $vehicle->gpsVehicle;
            if (!$gpsVehicle) return true; // No GPS means not 5G

            return $gpsVehicle->technology != '5G';
        });

        $this->info("Dispatch registers after non-5G filter: " . $dispatchRegisters->count());
        $specialCountAfter = $dispatchRegisters->filter(function($dr) use ($specialRoutes, $specialVehicles) {
            return in_array($dr->route_id, $specialRoutes) && in_array($dr->vehicle_id, $specialVehicles);
        })->count();
        $this->info("Special vehicles/routes after non-5G filter: " . $specialCountAfter);

        $count = 0;
        $fieldName = 'spreadsheet_passengers_sync';
        $this->info("Looking for observation field: {$fieldName}");

        foreach ($dispatchRegisters as $dispatchRegister) {
            $isSpecial = in_array($dispatchRegister->route_id, $specialRoutes) && in_array($dispatchRegister->vehicle_id, $specialVehicles);

            // Find the observation without using __()
            $observation = $dispatchRegister->drObservations->where('field', $fieldName)->first();
            
            // Try with __() just in case it was stored translated
            if (!$observation) {
                $observation = $dispatchRegister->drObservations->where('field', __('spreadsheet_passengers_sync'))->first();
            }

            if ($isSpecial) {
                $obsValue = $observation ? $observation->value : 'NOT FOUND';
                // Use getObservation to force finding it the exact same way the rest of the system does
                $forceObservation = $dispatchRegister->getObservation('spreadsheet_passengers_sync');
                $forceObsValue = $forceObservation ? $forceObservation->value : 'NOT FOUND';
                
                $this->info("DEBUG Special DR ID: {$dispatchRegister->id} | Route: {$dispatchRegister->route_id} | Vehicle: {$dispatchRegister->vehicle_id} | Obs: {$obsValue} | Forced Obs: {$forceObsValue}");
                
                // Use forced observation for our special cases if we didn't find the regular one
                if (!$observation && $forceObservation && is_numeric($forceObservation->value)) {
                    $observation = $forceObservation;
                }
            }

            if ($observation && is_numeric($observation->value) && $observation->value > 0) {
                $baseValue = intval($observation->value);

                // Logic based on route_id
                if (in_array($dispatchRegister->route_id, [347, 348])) {
                    $min = max(0, $baseValue - 2); // Prevent negative values
                    $max = $baseValue + 1;
                } elseif (in_array($dispatchRegister->route_id, [342, 341, 332, 331, 288, 287, 286, 285]) || in_array($dispatchRegister->route_id, [279, 280, 282, 283])) {
                    $min = max(0, $baseValue - 5); // Prevent negative values
                    $max = $baseValue + 2;
                } else {
                    $min = max(0, $baseValue - 10); // Prevent negative values
                    $max = $baseValue + 2;
                }

                $randomValue = rand($min, $max);

                DB::table('registrodespacho')
                    ->where('id_registro', $dispatchRegister->id)
                    ->update(['final_sensor_counter' => $randomValue]);

                $count++;
                $this->info("Updated Dispatch ID: {$dispatchRegister->id} | Base: {$baseValue} | New Sensor: {$randomValue}");
            }
        }

        $this->info("Finished. Updated {$count} dispatch registers.");
    }
}
