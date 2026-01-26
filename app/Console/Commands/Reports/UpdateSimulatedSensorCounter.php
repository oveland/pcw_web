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

        $routes = [285, 286, 287, 288, 321, 322, 331, 332, 337, 338, 341, 342, 343, 344];
        $tenDaysAgo = Carbon::now()->subDays(10)->startOfDay();

        // Target dispatch registers
        $dispatchRegisters = DispatchRegister::whereIn('route_id', $routes)
            ->where('date', '>=', $tenDaysAgo)
            ->where(function ($query) {
                $query->whereNull('final_sensor_counter')
                      ->orWhere('final_sensor_counter', 0);
            })
            ->with(['drObservations', 'vehicle', 'vehicle.gpsVehicle'])
            ->get();

        // Filter non-5G vehicles manually
        $dispatchRegisters = $dispatchRegisters->filter(function ($dispatchRegister) {
            $vehicle = $dispatchRegister->vehicle;
            if (!$vehicle) return false;
            
            $gpsVehicle = $vehicle->gpsVehicle;
            if (!$gpsVehicle) return true; // No GPS means not 5G

            return $gpsVehicle->technology != '5G';
        });

        $count = 0;
        $fieldName = __('spreadsheet_passengers_sync');

        foreach ($dispatchRegisters as $dispatchRegister) {
            // Find the observation
            $observation = $dispatchRegister->drObservations->where('field', $fieldName)->first();

            if ($observation && is_numeric($observation->value) && $observation->value > 0) {
                $baseValue = intval($observation->value);

                // Logic: Random value between (Base - 10) and (Base + 2)
                $min = max(0, $baseValue - 10); // Prevent negative values
                $max = $baseValue + 2;

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
