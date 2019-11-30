<?php

use App\Facades\BEADB;
use App\Models\BEA\Turn;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;
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
        $lastIdMigrated = Turn::max('id');
        $turns = BEADB::select("SELECT * FROM A_TURNO WHERE ATR_IDTURNO > $lastIdMigrated");

        $maxSequence = collect(\DB::select("SELECT max(id_crear_vehiculo) max FROM crear_vehiculo"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE vehicles_id_seq RESTART WITH $maxSequence");

        foreach ($turns as $turnBEA) {
            $turn = Turn::find($turnBEA->ATR_IDTURNO);
            $route = $this->validateRoute($turnBEA->ATR_IDRUTA);
            $driver = $this->validateDriver($turnBEA->ATR_IDCONDUCTOR);
            $vehicle = $this->validateVehicle($turnBEA->ATR_IDAUTOBUS);

            if (!$turn) $turn = new Turn();
            $turn->id = $turnBEA->ATR_IDTURNO;
            $turn->route_id = $route->id;
            $turn->driver_id = $driver->id;
            $turn->vehicle_id = $vehicle->id;

            if (!$turn->save()) {
                throw new Exception("Error saving TURN with id: $turnBEA->ATR_IDTURNO");
            }
        }

        $maxSequence = Vehicle::max('id') + 1;
        DB::statement("ALTER SEQUENCE crear_vehiculo_id_crear_vehiculo_seq RESTART WITH $maxSequence");
    }

    /**
     * @param $routeId
     * @return Route|Model|null
     * @throws Exception
     */
    public function validateRoute($routeId)
    {
        $route = Route::where('bea_id', $routeId)->first();

        if (!$route) {
            $routeBEA = BEADB::select("SELECT * FROM CRU_IDRUTA = $routeId")->first();
            if ($routeBEA) {
                $route = new Route();
                $route->bea_id = $routeBEA->CRU_IDRUTA;
                $route->name = $routeBEA->CRU_DESCRIPCION;
                $route->distance = 0;
                $route->road_time = 0;
                $route->url = 'none';
                $route->company_id = Company::COODETRANS;
                $route->dispatch_id = 46;
                $route->active = true;

                if (!$route->save()) {
                    throw new Exception("Error on validation save ROUTE with id: $routeBEA->CRU_IDRUTA");
                }
            }
        }

        return $route;
    }

    /**
     * @param $driverId
     * @return Driver|Model|null
     * @throws Exception
     */
    public function validateDriver($driverId)
    {
        $driver = Driver::where('bea_id', $driverId)->first();

        if (!$driver) {
            $driverBEA = BEADB::select("SELECT * FROM C_CONDUCTOR WHERE CCO_IDCONDUCTOR = $driverId")->first();
            if ($driverBEA) {
                $driver = new Driver();
                $driver->bea_id = $driverBEA->CCO_IDCONDUCTOR;
                $driver->first_name = $driverBEA->CCO_NOMBRE;
                $driver->last_name = "$driverBEA->CCO_APELLIDOP $driverBEA->CCO_APELLIDOM";
                $driver->identity = $driverBEA->CCO_CLAVECOND;
                $driver->company_id = Company::COODETRANS;
                $driver->active = true;

                if (!$driver->saveData()) {
                    throw new Exception("Error on validation save DRIVER with id: $driverBEA->CCO_IDCONDUCTOR");
                }
            }
        }

        return $driver;
    }

    /**
     * @param $vehicleId
     * @return Vehicle|Model|null
     * @throws Exception
     */
    public function validateVehicle($vehicleId)
    {
        $vehicle = Vehicle::where('bea_id', $vehicleId)->first();

        if (!$vehicle) {
            $vehicleBEA = BEADB::select("SELECT * FROM C_AUTOBUS WHERE CAU_IDAUTOBUS = $vehicleId")->first();

            if($vehicleBEA){
                $vehicle = new Vehicle();
                $duplicatedPlates = BEADB::select("SELECT count(1) TOTAL FROM C_AUTOBUS WHERE CAU_PLACAS = '$vehicleBEA->CAU_PLACAS'")->first();

                if ($duplicatedPlates->TOTAL > 1) $vehicleBEA->CAU_PLACAS = "$vehicleBEA->CAU_PLACAS-$vehicleBEA->CAU_NUMECONOM";

                if ($vehicleBEA->CAU_PLACAS) {
                    $vehicle->id = Vehicle::max('id') + 1;
                    $vehicle->bea_id = $vehicleBEA->CAU_IDAUTOBUS;
                    $vehicle->plate = $vehicleBEA->CAU_PLACAS;
                    $vehicle->number = $vehicleBEA->CAU_NUMECONOM;
                    $vehicle->company_id = Company::COODETRANS;
                    $vehicle->active = true;
                    $vehicle->in_repair = false;

                    if (!$vehicle->save()) {
                        throw new Exception("Error saving VEHICLE with id: $vehicleBEA->CAU_IDAUTOBUS");
                    }
                }


            }
        }

        return $vehicle;
    }
}
