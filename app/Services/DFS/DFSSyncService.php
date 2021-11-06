<?php

namespace App\Services\DFS;

use App\Facades\DFSDB;
use App\Http\Controllers\Utils\Geolocation;
use App\Models\LM\Mark;
use App\Models\LM\Trajectory;
use App\Models\LM\Turn;
use App\Models\Drivers\Driver;
use App\Models\Routes\Route;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use App\Services\LM\SyncService;
use Carbon\Carbon;
use DB;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

class DFSSyncService extends SyncService
{
    protected $type = 'dfs';

    function locations(Vehicle $vehicle, $date)
    {

        $maxDateMigrated = Location::whereBetween('date', ["$date", "$date 23:59:00"])->where('vehicle_id', $vehicle->id)->max('date');

        $maxDateMigrated = $maxDateMigrated ? " AND FECHA_HORA > '$maxDateMigrated'" : "";

        $locationsDFS = DFSDB::select("SELECT * FROM REGISTRO WHERE FECHA_HORA BETWEEN '$date' AND '$date 23:59:00' AND ID_VEHICULO = '$vehicle->bea_id' $maxDateMigrated AND EXTENDIDO > 1 ORDER BY FECHA_HORA");

        $total = $locationsDFS->count();

        $this->log("Migrating $total locations for vehicle $vehicle->number | $vehicle->plate\n");
        $migrated = 0;

        $initial = Carbon::now();

        $prevLocation = $locationsDFS->first();
        $prevOrientation = 0;

        foreach ($locationsDFS as $locationDFS) {
            usleep(100 * 1000); // Important delay for prevent BD down by error "too many open connections". Recommended value > 100 ms
            $c = (object)[
                'latitude' => Geolocation::DMtoDD($locationDFS->LATITUD),
                'longitude' => Geolocation::DMtoDD($locationDFS->LONGITUD),
            ];
            $speed = $locationDFS->VELOCIDAD;

            $date = Carbon::createFromFormat("Y-m-d H:i:s", explode('.', $locationDFS->FECHA_HORA)[0]);
            $timestamp = $date->timestamp;

            $orientation = Geolocation::orientationDD($prevLocation->LATITUD, $prevLocation->LONGITUD, $locationDFS->LATITUD, $locationDFS->LONGITUD);
            $distance = Geolocation::getDistanceDD($prevLocation->LATITUD, $prevLocation->LONGITUD, $locationDFS->LATITUD, $locationDFS->LONGITUD);

            if ($distance < 20) $orientation = $prevOrientation;

            $client = new Client();
            $response = $client->get(config('gps.server.urlAPI') . "/gps/location/save", [
                'query' => [
                    'vehicle' => $vehicle->id,
                    'latitude' => $c->latitude,
                    'longitude' => $c->longitude,
                    'speed' => $speed,
                    'timestamp' => $timestamp,
                    'orientation' => $orientation,
                ]
            ]);

            $status = $response->getStatusCode();
            $migrated++;
            $message = json_decode($response->getBody(), false)->message;

            $this->log("  $migrated/$total $timestamp | " . $date->toTimeString() . " • " . ($status == 200 ? "OK" : $status) . " | $message \n");

            $prevLocation = $locationDFS;
            $prevOrientation = $orientation;
        }

        $this->log("Finish migration in " . Carbon::now()->from($initial) . "\n");
    }

    /**
     * Sync A_TURNO >> bea_turns
     *
     * @throws Exception
     */
    function turns()
    {
        $lastIdMigrated = Turn::where('company_id', $this->company->id)->max('bea_id');
        $lastIdMigrated = $lastIdMigrated ? $lastIdMigrated : 0;

        $turns = DFSDB::select("
            SELECT CONVERT(VARCHAR(256), m.ID_HISTORICO_RECAUDACION) TRN_ID

                 , CONVERT(VARCHAR(256), v.ID_VEHICULO)              V_ID
                 , v.PLACA                                           V_PLATE
                 , v.COD_VEHICULO                                    V_NUMBER
            
                 , CONVERT(VARCHAR(256), r.ID_RUTA)                  R_ID
                 , r.DESCRIPCION                                     R_NAME
            
                 , CONVERT(VARCHAR(256), c.ID_CONDUCTOR)             D_ID
                 , c.NOMBRE                                          D_NAME
                 , IIF(c.APELLIDO1 = c.NOMBRE, '', c.APELLIDO1)      D_SURNAME1
                 , IIF(c.APELLIDO2 = c.NOMBRE, '', c.APELLIDO2)      D_SURNAME2
                 , c.COD_CONDUCTOR                                   D_CODE
            FROM HISTORICO_RECAUDACION m,
                 VEHICULO v,
                 RUTA r,
                 CONDUCTOR c
            WHERE m.ID_VEHICULO = v.ID_VEHICULO
              AND m.ID_RUTA = r.ID_RUTA
              AND m.ID_CONDUCTOR = c.ID_CONDUCTOR
              AND m.ID_HISTORICO_RECAUDACION > '$lastIdMigrated'
              AND v.ID_VEHICULO = " . ($this->vehicle->bea_id ? "'" . $this->vehicle->bea_id . "'" : 'NULL') . "
              AND r.DESCRIPCION in ('CALI - PALMIRA', 'PALMIRA - CALI', 'CALI - ZAMORANO', 'ZAMORANO - CALI', 'CALI - AEROPUERTO',
                                    'AEROPUERTO - CALI')
        ");

        $maxSequence = collect(DB::select("SELECT max(id_crear_vehiculo) max FROM crear_vehiculo"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE vehicles_id_seq RESTART WITH $maxSequence");

        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_turns"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_turns_id_seq RESTART WITH $maxSequence");

        $this->log("Sync " . $turns->count() . " $turns from LM");

        foreach ($turns as $turnDFS) {
            $this->validateTurn($turnDFS->TRN_ID, $turnDFS);
        }

        $maxSequence = Vehicle::max('id') + 1;
        DB::statement("ALTER SEQUENCE crear_vehiculo_id_crear_vehiculo_seq RESTART WITH $maxSequence");
    }

    /**
     * Sync C_AUTOBUS >> vehicles
     * @throws Exception
     */
    function vehicles()
    {
        $maxSequence = collect(DB::select("SELECT max(id_crear_vehiculo) max FROM crear_vehiculo"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE vehicles_id_seq RESTART WITH $maxSequence");

        $vehicles = DFSDB::select("SELECT CONVERT(VARCHAR(256), ID_VEHICULO) V_ID, PLACA V_PLATE, COD_VEHICULO V_NUMBER FROM VEHICULO");

        $this->log("Sync " . $vehicles->count() . " vehicles from LM");

        $detectedVehicles = collect([]);
        foreach ($vehicles as $vehicleDFS) {
            $this->validateVehicle($vehicleDFS->V_ID, $vehicleDFS, $detectedVehicles);
        }

        $maxSequence = Vehicle::max('id') + 1;
        DB::statement("ALTER SEQUENCE crear_vehiculo_id_crear_vehiculo_seq RESTART WITH $maxSequence");
    }

    /**
     * Sync C_CONDUCTOR >> drivers
     * @throws Exception
     */
    function drivers()
    {
        $maxSequence = Driver::max('id') + 1;
        DB::statement("ALTER SEQUENCE conductor_id_idconductor_seq RESTART WITH $maxSequence");

        $drivers = DFSDB::select("
            SELECT CONVERT(VARCHAR(256), c.ID_CONDUCTOR)        D_ID
                 , c.NOMBRE                                     D_NAME
                 , IIF(c.APELLIDO1 = c.NOMBRE, '', c.APELLIDO1) D_SURNAME1
                 , IIF(c.APELLIDO2 = c.NOMBRE, '', c.APELLIDO2) D_SURNAME2
                 , c.COD_CONDUCTOR                              D_CODE
            FROM CONDUCTOR c
        ");

        $this->log("Sync " . $drivers->count() . " drivers from LM");

        foreach ($drivers as $driverDFS) {
            $this->validateDriver($driverDFS->D_ID, $driverDFS);
        }

        $maxSequence = Driver::max('id') + 1;
        DB::statement("ALTER SEQUENCE conductor_id_idconductor_seq RESTART WITH $maxSequence");
    }

    /**
     * Sync C_RUTA >> routes
     * @throws Exception
     */
    function routes()
    {
        $maxSequenceRuta = collect(DB::select("SELECT max(id_rutas) max FROM ruta"))->first()->max + 1;
        $maxSequenceRoutes = collect(DB::select("SELECT max(id) max FROM routes"))->first()->max + 1;
        $maxSequence = max([$maxSequenceRoutes, $maxSequenceRuta]);
        DB::statement("ALTER SEQUENCE routes_id_seq RESTART WITH $maxSequence");

        $routes = DFSDB::select("
            SELECT CONVERT(VARCHAR(256), r.ID_RUTA)                 R_ID
                 , r.DESCRIPCION                                    R_NAME
            FROM RUTA r
            WHERE DESCRIPCION IN ('CALI - PALMIRA', 'PALMIRA - CALI', 'CALI - ZAMORANO', 'ZAMORANO - CALI', 'CALI - AEROPUERTO',
                                  'AEROPUERTO - CALI')
        ");

        $this->log("Sync " . $routes->count() . " routes from LM");

        foreach ($routes as $routeDFS) {
            $this->validateRoute($routeDFS->R_ID, $routeDFS);
        }

        $maxSequence = Route::max('id') + 1;
        DB::statement("ALTER SEQUENCE ruta_id_rutas_seq RESTART WITH $maxSequence");
    }

    /**
     * Sync A_DERROTERO >> bea_trajectories
     * @throws Exception
     */
    function trajectories()
    {
        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_trajectories"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_trajectories_id_seq RESTART WITH $maxSequence");


        $trajectories = DFSDB::select("
            SELECT CONVERT(VARCHAR(256), m.ID_HISTORICO_RECAUDACION) TRJ_ID

                 , CONVERT(VARCHAR(256), v.ID_VEHICULO)              V_ID
                 , v.PLACA                                           V_PLATE
                 , v.COD_VEHICULO                                    V_NUMBER
            
                 , CONVERT(VARCHAR(256), r.ID_RUTA)                  R_ID
                 , r.DESCRIPCION                                     R_NAME
            
                 , CONVERT(VARCHAR(256), c.ID_CONDUCTOR)             D_ID
                 , c.NOMBRE                                          D_NAME
                 , IIF(c.APELLIDO1 = c.NOMBRE, '', c.APELLIDO1)      D_SURNAME1
                 , IIF(c.APELLIDO2 = c.NOMBRE, '', c.APELLIDO2)      D_SURNAME2
                 , c.COD_CONDUCTOR                                   D_CODE
            FROM HISTORICO_RECAUDACION m,
                 VEHICULO v,
                 RUTA r,
                 CONDUCTOR c
            WHERE m.ID_VEHICULO = v.ID_VEHICULO
              AND m.ID_RUTA = r.ID_RUTA
              AND m.ID_CONDUCTOR = c.ID_CONDUCTOR
              AND m.FECHA_HORA_INICIO > '2021-09-30'
              AND r.DESCRIPCION in ('CALI - PALMIRA', 'PALMIRA - CALI', 'CALI - ZAMORANO', 'ZAMORANO - CALI', 'CALI - AEROPUERTO',
                                    'AEROPUERTO - CALI')
        ");

        $this->log("Sync " . $trajectories->count() . " trajectories from LM");

        foreach ($trajectories as $trajectoryDFS) {
            $this->validateTrajectory($trajectoryDFS->TRJ_ID, $trajectoryDFS);
        }
    }

    /**
     * Sync A_MARCA >> bea_marks
     *
     * @throws Exception
     * @throws Throwable
     */
    function marks()
    {
        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_marks"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_marks_id_seq RESTART WITH $maxSequence");

        $marks = DFSDB::select("
            SELECT CONVERT(VARCHAR(256), m.ID_HISTORICO_RECAUDACION) ID

                 , CONVERT(VARCHAR(256), m.ID_HISTORICO_RECAUDACION) TRN_ID
                 , CONVERT(VARCHAR(256), m.ID_HISTORICO_RECAUDACION) TRJ_ID
            
                 , CONVERT(VARCHAR(256), v.ID_VEHICULO)              V_ID
                 , v.PLACA                                           V_PLATE
                 , v.COD_VEHICULO                                    V_NUMBER
            
                 , CONVERT(VARCHAR(256), r.ID_RUTA)                  R_ID
                 , r.DESCRIPCION                                     R_NAME
            
                 , c.ID_CONDUCTOR                                    D_ID
                 , c.NOMBRE                                          D_NAME
                 , IIF(c.APELLIDO1 = c.NOMBRE, '', c.APELLIDO1)      D_SURNAME1
                 , IIF(c.APELLIDO2 = c.NOMBRE, '', c.APELLIDO2)      D_SURNAME2
                 , c.COD_CONDUCTOR                                   D_CODE
            
                 , FECHA_HORA_INICIO                                 INITIAL_DATE
                 , FECHA_HORA_FIN                                    FINAL_DATE
                 , PASAJEROS                                         PASSENGERS
                 , MONTO                                             TOTAL
                 , ENTREGA                                           TAKEN
                 , BLOQUEOS                                          LOCKS
            FROM HISTORICO_RECAUDACION m,
                 VEHICULO v,
                 RUTA r,
                 CONDUCTOR c
            WHERE m.ID_VEHICULO = v.ID_VEHICULO
              AND m.ID_RUTA = r.ID_RUTA
              AND m.ID_CONDUCTOR = c.ID_CONDUCTOR
              AND m.FECHA_HORA_INICIO > " . ($this->date ? "'$this->date'" : 'current_date - 30') . "
              AND v.ID_VEHICULO = " . ($this->vehicle->bea_id ? "'" . $this->vehicle->bea_id . "'" : 'NULL') . "
              AND r.DESCRIPCION in ('CALI - PALMIRA', 'PALMIRA - CALI', 'CALI - ZAMORANO', 'ZAMORANO - CALI', 'CALI - AEROPUERTO',
                                    'AEROPUERTO - CALI')
        ");

        foreach ($marks as $markDFS) {
            DB::transaction(function () use ($markDFS) {
                $this->validateMark($markDFS);
            });
        }

        if ($this->vehicle && $this->date) {
            $markIdsDFS = $marks->pluck('ID');

            $turns = Turn::where('company_id', $this->company->id)->where('vehicle_id', $this->vehicle->id)->get();
            $marksIdsPCW = Mark::where('company_id', $this->company->id)
                ->whereIn('turn_id', $turns->pluck('id'))
                ->where('liquidated', false)
                ->whereDate('date', $this->date)
                ->get()->pluck('bea_id');

            $duplicatedIdsPCW = $marksIdsPCW->diff($markIdsDFS)->implode(',');
            if ($duplicatedIdsPCW) {
                $companyId = $this->company->id;
                DB::statement("UPDATE bea_marks SET duplicated = TRUE WHERE company_id = $companyId AND bea_id IN ($duplicatedIdsPCW)");
            }
        }

        if ($this->vehicle && $this->date) DB::select("SELECT refresh_bea_marks_turns_numbers_function(" . $this->vehicle->id . ", '$this->date')");
    }

    /**
     * @param $markDFS
     * @return Mark
     * @throws Exception
     */
    function validateMark($markDFS)
    {
        $mark = Mark::where('company_id', $this->company->id)->where('bea_id', $markDFS->ID)->first();

        if (!$mark) $mark = new Mark();
        else if ($mark->liquidated) return null;

        $passengersUp = 0;
        $passengersDown = 0;

        $locks = $markDFS->LOCKS;
        $auxiliaries = 0;

        $imBeaMax = 0;
        $imBeaMin = 0;

        $passengersBoarding = $passengersUp > $passengersDown ? ($passengersUp - $passengersDown) : 0;

        $passengersDFS = $markDFS->PASSENGERS;
        $totalDFS = intval($markDFS->TOTAL);

        $turn = Turn::where('bea_id', $markDFS->TRN_ID)->where('company_id', $this->company->id)->first();
        if (!$turn) $turn = $this->validateTurn($markDFS->TRN_ID, $markDFS);

        $trajectory = Trajectory::where('bea_id', $markDFS->TRJ_ID)->where('company_id', $this->company->id)->first();
        if (!$trajectory) $trajectory = $this->validateTrajectory($markDFS->TRJ_ID, $markDFS);

        $initialTime = Carbon::createFromFormat("Y-m-d H:i:s", explode('.', $markDFS->INITIAL_DATE)[0]);
        $finalTime = Carbon::createFromFormat("Y-m-d H:i:s", explode('.', $markDFS->FINAL_DATE)[0]);

        $dateTime = $initialTime->toDateString() == $finalTime->toDateString() ? $initialTime : $finalTime;

        $mark->company_id = $this->company->id;
        $mark->bea_id = $markDFS->ID;
        $mark->turn_id = $turn->id;
        $mark->trajectory_id = $trajectory ? $trajectory->id : null;
        $mark->date = $dateTime->format(config('app.simple_date_time_format'));
        $mark->initial_time = $initialTime->format(config('app.simple_time_format'));
        $mark->final_time = $finalTime->format(config('app.simple_time_format'));
        $mark->passengers_up = $passengersUp;
        $mark->passengers_down = $passengersDown;
        $mark->locks = $locks;
        $mark->auxiliaries = $auxiliaries;
        $mark->boarded = $passengersBoarding;
        $mark->im_bea_max = $imBeaMax;
        $mark->im_bea_min = $imBeaMin;
        $mark->total_bea = $totalDFS;
        $mark->passengers_bea = $passengersDFS;

        if (!$mark->save()) {
            throw new Exception("Error saving MARK with id: $markDFS->ID");
        }

        return $mark;
    }


    /**
     * @param $turnDFSId
     * @param null $data
     * @return Turn|Model|null
     * @throws Exception
     */
    function validateTurn($turnDFSId, $data = null)
    {
        $turn = Turn::where('bea_id', $turnDFSId)->where('company_id', $this->company->id)->first();

        if ($turnDFSId) {
            $turnDFS = $data;
            if ($turnDFS) {
                if (!$turn) {
                    $turn = new Turn();
                }

                if (!$turn->driver_id || !$turn->route_id || !$turn->vehicle_id) {
                    $route = $this->validateRoute($turnDFS->R_ID, $data);
                    $driver = $this->validateDriver($turnDFS->D_ID, $data);
                    $vehicle = $this->validateVehicle($turnDFS->V_ID, $data);
                    $turn->company_id = $this->company->id;
                    $turn->bea_id = $turnDFS->TRN_ID;
                    $turn->route_id = $route->id;
                    $turn->driver_id = $driver ? $driver->id : null;
                    $turn->vehicle_id = $vehicle->id;

                    if (!$turn->save()) {
                        throw new Exception("Error saving TURN with id: $turnDFS->TRN_ID");
                    }
                }
            }
        }

        return $turn;
    }

    /**
     * @param $trajectoryDFSId
     * @param $data
     * @return Trajectory|Model|null
     * @throws Exception
     */
    function validateTrajectory($trajectoryDFSId, $data = null)
    {
        $route = $this->validateRoute($data->R_ID);

        $trajectory = Trajectory::where('name', $data->R_NAME)
            ->where('route_id', $route->id)
            ->where('company_id', $this->company->id)->first();

        if (!$trajectory && $trajectoryDFSId) {
            $trajectoryDFS = $data;

            if ($trajectoryDFS) {
                $trajectory = new Trajectory();
                $trajectory->company_id = $this->company->id;
                $trajectory->bea_id = $trajectoryDFS->TRJ_ID;
                $trajectory->name = $trajectoryDFS->R_NAME;
                $trajectory->route_id = $route->id;
                $trajectory->description = "$trajectoryDFS->R_NAME";

                if (!$trajectory->save()) {
                    throw new Exception("Error saving TRAJECTORY with id: $trajectoryDFS->TRJ_ID");
                }
            }
        }

        return $trajectory;
    }

    /**
     * @param $routeDFSId
     * @param $data
     * @return Route|integer|null
     * @throws Exception
     */
    private function validateRoute($routeDFSId, $data = null)
    {
        $route = Route::where('bea_id', $routeDFSId)->where('company_id', $this->company->id)->first();

        if (!$route && $routeDFSId) {
            $routeDFS = $data;
            if ($routeDFS) {
                $route = new Route();
                $route->bea_id = $routeDFS->R_ID;
                $route->name = "DFS | $routeDFS->R_NAME";
                $route->distance = 0;
                $route->road_time = 0;
                $route->url = 'none';
                $route->company_id = $this->company->id;
                $route->dispatch_id = 100;
                $route->active = true;
                $route->created_at = Carbon::now();
                $route->updated_at = Carbon::now();

                $this->log("Migrated route with bea_id = $route->bea_id");

                if (!$route->save()) {
                    throw new Exception("Error on validation save ROUTE with id: $routeDFS->R_ID");
                }
            }
        }

        return $route;
    }

    /**
     * @param $driverDFSId
     * @param $data
     * @return Driver|Model|null
     * @throws Exception
     */
    private function validateDriver($driverDFSId, $data = null)
    {
        $driver = null;
        if ($driverDFSId) {
            $driver = Driver::where('bea_id', $driverDFSId)->where('company_id', $this->company->id)->first();

            if (!$driver) {
                $driverDFS = $data;

                if ($driverDFS) {

                    $driver = $this->company->drivers->filter(function (Driver $d) use ($driverDFS) {
                        return trim(Str::upper($d->code)) == trim(Str::upper($driverDFS->D_CODE));
                    })->first();

                    if ($driver) {
                        $this->log("  Driver for bea_id = $driverDFSId, code $driverDFS->D_CODE >> SYNC driver: id $driver->id, code $driver->code ($driver->full_name)");

                        $driver->bea_id = $driverDFSId;
                        $driver->saveData();
                    } else {
                        $this->log("  Driver with bea_id $driverDFSId, code $driverDFS->D_ID is not migrated yet");

                        $driver = new Driver();
                        $driver->bea_id = $driverDFS->D_ID;
                        $driver->first_name = $driverDFS->D_NAME;
                        $driver->last_name = "$driverDFS->D_SURNAME1 $driverDFS->D_SURNAME2";
                        $driver->identity = $driverDFS->D_CODE;
                        $driver->company_id = $this->company->id;
                        $driver->active = true;

                        if (!$driver->saveData()) {
                            $error = "Error on validation save DRIVER with id: $driverDFS->D_ID";
                            $this->log($error);
                            throw new Exception($error);
                        } else {
                            $this->log("Migrated driver with bea_id = $driver->bea_id");
                        }
                    }
                }
            }
        }

        return $driver;
    }

    /**
     * @param $vehicleDFSId
     * @param null $data
     * @param null $detectedVehicles
     * @return Vehicle|integer|null
     * @throws Exception
     */
    private function validateVehicle($vehicleDFSId, $data = null, &$detectedVehicles = null)
    {
        $vehicle = null;
        if ($vehicleDFSId) {
            $vehicle = Vehicle::where('bea_id', $vehicleDFSId)->where('company_id', $this->company->id)->first();

            if (!$vehicle) {
                $vehicleDFS = $data ?: DFSDB::select("SELECT ID_VEHICULO V_ID, PLACA V_PLATE, COD_VEHICULO V_NUMBER FROM VEHICULO WHERE ID_VEHICULO = $vehicleDFSId")->first();

                if ($vehicleDFS) {

                    $vehicle = $this->company->vehicles->filter(function (Vehicle $v) use ($vehicleDFS) {
                        return Str::upper(str_replace('-', '', $v->plate)) == Str::upper(str_replace('-', '', $vehicleDFS->V_PLATE)) || Str::upper($v->number) == Str::upper($vehicleDFS->V_NUMBER);
                    })->first();

                    if ($vehicle) {
                        $duplicated = $detectedVehicles ? $detectedVehicles->get($vehicle->id) : null;

                        $routeDefault = $vehicle->dispatcherVehicle ? $vehicle->dispatcherVehicle->route->name : '';
                        $this->log("  Vehicle For bea_id = $vehicleDFSId, number $vehicleDFS->V_NUMBER and plate $vehicleDFS->V_PLATE >> SYNC vehicle: id $vehicle->id, number $vehicle->number ($vehicle->plate) and Route = $routeDefault " . ($duplicated ? " ***** DUPLICATED *******" : ""));

                        $vehicle->bea_id = $vehicleDFSId;
                        $vehicle->save();

                        if ($detectedVehicles) {
                            $detectedVehicles->put($vehicle->id, $vehicle);
                        }
                    } else {
                        $this->log("  Vehicle with bea_id $vehicleDFSId, number $vehicleDFS->V_NUMBER and plate $vehicleDFS->V_PLATE is not migrated yet");

                        $vehicle = new Vehicle();
                        $duplicatedPlates = DFSDB::select("SELECT count(1) TOTAL FROM VEHICULO WHERE PLACA = '$vehicleDFS->V_PLATE'")->first();

                        if ($duplicatedPlates->TOTAL > 1) {
                            $vehicleDFS->V_PLATE = "$vehicleDFS->V_PLATE#$vehicleDFS->V_NUMBER";
                        }

                        if ($vehicleDFS->V_PLATE) {
                            $vehicle->id = Vehicle::max('id') + 1;
                            $vehicle->bea_id = $vehicleDFS->V_ID;
                            $vehicle->plate = $vehicleDFS->V_PLATE;
                            $vehicle->number = $vehicleDFS->V_NUMBER;
                            $vehicle->company_id = $this->company->id;
                            $vehicle->active = true;
                            $vehicle->in_repair = false;

                            $this->log("Migrated vehicle with bea_id = $vehicle->bea_id");

                            if (!$vehicle->save()) {
                                throw new Exception("Error saving VEHICLE with id: $vehicleDFS->V_ID");
                            } else {
                                $this->checkVehicleParams($vehicle);
                            }
                        }
                    }
                }
            }
        }

        return $vehicle;
    }
}