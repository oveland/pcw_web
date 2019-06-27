<?php

use App\Facades\BEADB;
use App\Models\BEA\Turn;
use App\Models\Drivers\Driver;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Seeder;

class TurnsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $turns = BEADB::select("SELECT * FROM A_TURNO WHERE ATR_FECHATURNO >= '2019-03-01'");

        foreach ($turns as $turnBEA) {
            $turn = Turn::find($turnBEA->ATR_IDTURNO);
            $route = Route::where('bea_id', $turnBEA->ATR_IDRUTA)->get()->first();
            $driver = Driver::where('bea_id', $turnBEA->ATR_IDCONDUCTOR)->get()->first();
            $vehicle = Vehicle::where('bea_id', $turnBEA->ATR_IDAUTOBUS)->get()->first();

            if (!$turn) $turn = new Turn();

            $turn->id = $turnBEA->ATR_IDTURNO;
            $turn->route_id = $route->id;
            $turn->driver_id = $driver->id;
            $turn->vehicle_id = $vehicle->id;

            if (!$turn->save()) {
                throw new Exception("Error saving TURN with id: $turnBEA->ATR_IDTURNO");
            }
        }
    }
}
