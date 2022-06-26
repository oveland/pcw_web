<?php

namespace App\Services\BEA;

use App\Facades\BEADB;
use App\Models\LM\Mark;
use App\Models\LM\Trajectory;
use App\Models\LM\Turn;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use App\Services\LM\SyncService;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

class BEASyncService extends SyncService
{
    protected $type = 'bea';

    function locations(Vehicle $vehicle, $date)
    {

    }

    /**
     * Sync A_TURNO >> bea_turns
     *
     * @throws Exception
     */
    public function turns()
    {
        $lastIdMigrated = Turn::where('company_id', $this->company->id)->max('bea_id');
        $lastIdMigrated = $lastIdMigrated ? $lastIdMigrated : 0;
        $turns = BEADB::for($this->company, $this->dbId)->select("SELECT * FROM A_TURNO WHERE ATR_IDTURNO > $lastIdMigrated");

        $maxSequence = collect(DB::select("SELECT max(id_crear_vehiculo) max FROM crear_vehiculo"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE vehicles_id_seq RESTART WITH $maxSequence");

        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_turns"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_turns_id_seq RESTART WITH $maxSequence");

        $this->log("Sync " . $turns->count() . " $turns from LM");

        foreach ($turns as $turnBEA) {
            $this->validateTurn($turnBEA->ATR_IDTURNO, $turnBEA);
        }

        $maxSequence = Vehicle::max('id') + 1;
        DB::statement("ALTER SEQUENCE crear_vehiculo_id_crear_vehiculo_seq RESTART WITH $maxSequence");
    }

    /**
     * Sync C_AUTOBUS >> vehicles
     * @throws Exception
     */
    public function vehicles()
    {
        $maxSequence = collect(DB::select("SELECT max(id_crear_vehiculo) max FROM crear_vehiculo"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE vehicles_id_seq RESTART WITH $maxSequence");

        $vehicles = BEADB::for($this->company, $this->dbId)->select("SELECT * FROM C_AUTOBUS WHERE CAU_NUMECONOM like '%20%'");

        $this->log("Sync " . $vehicles->count() . " vehicles from LM");

        $detectedVehicles = collect([]);
        foreach ($vehicles as $vehicleBEA) {
            $this->validateVehicle($vehicleBEA->CAU_IDAUTOBUS, $vehicleBEA, $detectedVehicles);
        }

        $maxSequence = Vehicle::max('id') + 1;
        DB::statement("ALTER SEQUENCE crear_vehiculo_id_crear_vehiculo_seq RESTART WITH $maxSequence");
    }

    /**
     * Sync C_CONDUCTOR >> drivers
     * @throws Exception
     */
    public function drivers()
    {
        $maxSequence = Driver::max('id') + 1;
        DB::statement("ALTER SEQUENCE conductor_id_idconductor_seq RESTART WITH $maxSequence");

        $drivers = BEADB::select("SELECT * FROM C_CONDUCTOR");

        $this->log("Sync " . $drivers->count() . " drivers from LM");

        foreach ($drivers as $driverBEA) {
            $this->validateDriver($driverBEA->CCO_IDCONDUCTOR, $driverBEA);
        }

        $maxSequence = Driver::max('id') + 1;
        DB::statement("ALTER SEQUENCE conductor_id_idconductor_seq RESTART WITH $maxSequence");
    }

    /**
     * Sync C_RUTA >> routes
     * @throws Exception
     */
    public function routes()
    {
        $maxSequence = collect(DB::select("SELECT max(id_rutas) max FROM ruta"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE routes_id_seq RESTART WITH $maxSequence");

        $routes = BEADB::for($this->company, $this->dbId)->select("SELECT * FROM C_RUTA");

        $this->log("Sync " . $routes->count() . " routes from LM");

        foreach ($routes as $routeBEA) {
            $this->validateRoute($routeBEA->CRU_IDRUTA, $routeBEA);
        }

        $maxSequence = Route::max('id') + 1;
        DB::statement("ALTER SEQUENCE ruta_id_rutas_seq RESTART WITH $maxSequence");
    }

    /**
     * Sync A_DERROTERO >> bea_trajectories
     * @throws Exception
     */
    public function trajectories()
    {
        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_trajectories"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_trajectories_id_seq RESTART WITH $maxSequence");


        $trajectories = BEADB::for($this->company, $this->dbId)->select("SELECT * FROM C_DERROTERO");

        $this->log("Sync " . $trajectories->count() . " trajectories from LM");

        foreach ($trajectories as $trajectoryBEA) {
            $this->validateTrajectory($trajectoryBEA->CDR_IDDERROTERO, $trajectoryBEA);
        }
    }

    /**
     * Sync A_MARCA >> bea_marks
     *
     * @throws Exception
     * @throws Throwable
     */
    public function marks()
    {
        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_marks"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_marks_id_seq RESTART WITH $maxSequence");

        $queryVehicle = $this->vehicle ? "AND AMR_IDTURNO IN (SELECT ATR_IDTURNO FROM A_TURNO WHERE ATR_IDAUTOBUS = " . ($this->vehicle->bea_id ?? 0) . ")" : "";
        $queryMarks = "SELECT * FROM A_MARCA WHERE (AMR_FHINICIO > " . ($this->date ? "'$this->date'" : 'current_date - 30') . ") $queryVehicle";
        $marks = BEADB::for($this->company, $this->dbId)->select($queryMarks);

        foreach ($marks as $markBEA) {
            DB::transaction(function () use ($markBEA) {
                $this->validateMark($markBEA);
            });
        }

        if ($this->vehicle && $this->date) {
            $markIdsBEA = $marks->pluck('AMR_IDMARCA');

            $turns = Turn::where('company_id', $this->company->id)->where('vehicle_id', $this->vehicle->id)->get();
            $marksIdsPCW = Mark::where('company_id', $this->company->id)
                ->whereIn('turn_id', $turns->pluck('id'))
                ->where('liquidated', false)
                ->whereDate('date', $this->date)
                ->get()->pluck('bea_id');

            $duplicatedIdsPCW = $marksIdsPCW->diff($markIdsBEA)
                ->map(function ($m) {
                    return "$m::VARCHAR";
                })
                ->implode(',');
            if ($duplicatedIdsPCW) {
                $companyId = $this->company->id;
                DB::statement("UPDATE bea_marks SET duplicated = TRUE WHERE company_id = $companyId AND bea_id IN ($duplicatedIdsPCW)");
            }
        }

        if ($this->vehicle && $this->date) DB::select("SELECT refresh_bea_marks_turns_numbers_function(" . $this->vehicle->id . ", '$this->date')");
    }

    /**
     * @param $markBEA
     * @return Mark
     * @throws Exception
     */
    function validateMark($markBEA)
    {
        $mark = Mark::where('company_id', $this->company->id)->where('bea_id', $markBEA->AMR_IDMARCA)->first();

        if (!$mark) $mark = new Mark();
        else if ($mark->liquidated) return null;

        $passengersUp = $markBEA->AMR_SUBIDAS;
        $passengersDown = $markBEA->AMR_BAJADAS;

        $locks = $markBEA->AMR_BLOQUEOS;
        $auxiliaries = $markBEA->AMR_AUXILIARES;

        $imBeaMax = $markBEA->AMR_IMEBEAMAX;
        $imBeaMin = $markBEA->AMR_IMEBEAMIN;

        $passengersBoarding = $passengersUp > $passengersDown ? ($passengersUp - $passengersDown) : 0;

        $passengersBEA = $passengersUp > $passengersDown ? $passengersUp : $passengersDown;
        $totalBEA = (($imBeaMax + $imBeaMin) / 2) * 2500;

        if ($this->company->id == Company::ALAMEDA) {
            $totalBEA = $passengersBEA * 2200;
        }

        // Synchronized models
        $turn = Turn::where('bea_id', $markBEA->AMR_IDTURNO)->where('company_id', $this->company->id)->first();
        if (!$turn) $turn = $this->validateTurn($markBEA->AMR_IDTURNO);

        $trajectory = Trajectory::where('bea_id', $markBEA->AMR_IDDERROTERO)->where('company_id', $this->company->id)->first();
        if (!$trajectory) $trajectory = $this->validateTrajectory($markBEA->AMR_IDDERROTERO);

        $initialTime = Carbon::createFromFormat("Y-m-d H:i:s", $markBEA->AMR_FHINICIO);
        $finalTime = Carbon::createFromFormat("Y-m-d H:i:s", $markBEA->AMR_FHFINAL);

        $dateTime = $initialTime->toDateString() == $finalTime->toDateString() ? $initialTime : $finalTime;

        $mark->company_id = $this->company->id;
        $mark->bea_id = $markBEA->AMR_IDMARCA;
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
        $mark->total_bea = ceil($totalBEA);
        $mark->passengers_bea = $passengersBEA;

        if (!$mark->save()) {
            throw new Exception("Error saving MARK with id: $markBEA->AMR_IDMARCA");
        }

        return $mark;
    }


    /**
     * @param $turnBEAId
     * @param null $data
     * @return Turn|Model|null
     * @throws Exception
     */
    public function validateTurn($turnBEAId, $data = null)
    {
        $turn = Turn::where('bea_id', $turnBEAId)->where('company_id', $this->company->id)->first();

        if ($turnBEAId) {
            $turnBEA = $data ? $data : BEADB::for($this->company, $this->dbId)->select("SELECT * FROM A_TURNO WHERE ATR_IDTURNO = $turnBEAId")->first();
            if ($turnBEA) {
                if (!$turn) {
                    $turn = new Turn();
                }

                if (!$turn->driver_id || !$turn->route_id || !$turn->vehicle_id) {
                    $route = $this->validateRoute($turnBEA->ATR_IDRUTA);
                    $driver = $this->validateDriver($turnBEA->ATR_IDCONDUCTOR);
                    $vehicle = $this->validateVehicle($turnBEA->ATR_IDAUTOBUS);
                    $turn->company_id = $this->company->id;
                    $turn->bea_id = $turnBEA->ATR_IDTURNO;
                    $turn->route_id = $route->id;
                    $turn->driver_id = $driver ? $driver->id : null;
                    $turn->vehicle_id = $vehicle->id;

                    if (!$turn->save()) {
                        throw new Exception("Error saving TURN with id: $turnBEA->ATR_IDTURNO");
                    }
                }
            }
        }

        return $turn;
    }

    /**
     * @param $trajectoryBEAId
     * @param $data
     * @return Trajectory|Model|null
     * @throws Exception
     */
    public function validateTrajectory($trajectoryBEAId, $data = null)
    {
        $trajectory = Trajectory::where('bea_id', $trajectoryBEAId)->where('company_id', $this->company->id)->first();

        if (!$trajectory && $trajectoryBEAId) {
            $trajectoryBEA = $data ?: BEADB::for($this->company, $this->dbId)->select("SELECT * FROM C_DERROTERO WHERE CDR_IDDERROTERO = $trajectoryBEAId")->first();

            if ($trajectoryBEA) {
                $route = $this->validateRoute($trajectoryBEA->CDR_IDRUTA);

                $trajectory = new Trajectory();
                $trajectory->company_id = $this->company->id;
                $trajectory->bea_id = $trajectoryBEA->CDR_IDDERROTERO;
                $trajectory->name = $trajectoryBEA->CDR_DESCRIPCION;
                $trajectory->route_id = $route->id;
                $trajectory->description = "$trajectoryBEA->CDR_DESCRIPCION";

                if (!$trajectory->save()) {
                    throw new Exception("Error saving TRAJECTORY with id: $trajectoryBEA->CDR_IDDERROTERO");
                }
            }
        }

        return $trajectory;
    }

    /**
     * @param $routeBEAId
     * @param $data
     * @return Route|integer|null
     * @throws Exception
     */
    private function validateRoute($routeBEAId, $data = null)
    {
        $route = Route::where('bea_id', $routeBEAId)
            ->where('db_id', $this->dbId)
            ->where('company_id', $this->company->id)->first();

        if (!$route && $routeBEAId) {
            $routeBEA = $data ?: BEADB::for($this->company, $this->dbId)->select("SELECT * FROM C_RUTA WHERE CRU_IDRUTA = $routeBEAId")->first();
            if ($routeBEA) {
                $route = new Route();
                $route->bea_id = $routeBEA->CRU_IDRUTA;
                $route->name = "BEA | $routeBEA->CRU_DESCRIPCION";
                $route->distance = 0;
                $route->road_time = 0;
                $route->url = 'none';
                $route->company_id = $this->company->id;
                $route->dispatch_id = 46;
                $route->active = true;
                $route->created_at = Carbon::now();
                $route->updated_at = Carbon::now();

                $this->log("Migrated route with bea_id = $route->bea_id");

                if (!$route->save()) {
                    throw new Exception("Error on validation save ROUTE with id: $routeBEA->CRU_IDRUTA");
                }
            }
        }

        return $route;
    }

    /**
     * @param $driverBEAId
     * @param $data
     * @return Driver|Model|null
     * @throws Exception
     */
    private function validateDriver($driverBEAId, $data = null)
    {
        $driver = null;
        if ($driverBEAId) {
            $driver = Driver::where('bea_id', $driverBEAId)
                ->where('db_id', $this->dbId)
                ->where('company_id', $this->company->id)->first();

            if (!$driver) {
                $driverBEA = $data ?: BEADB::for($this->company, $this->dbId)->select("SELECT * FROM C_CONDUCTOR WHERE CCO_IDCONDUCTOR = $driverBEAId")->first();

                if ($driverBEA) {

                    $driver = $this->company->drivers->filter(function (Driver $d) use ($driverBEA) {
                        return trim(Str::upper($d->code)) == trim(Str::upper($driverBEA->CCO_CLAVECOND));
                    })->first();

                    if ($driver) {
                        $this->log("  Driver for bea_id = $driverBEAId, code $driverBEA->CCO_CLAVECOND >> SYNC driver: id $driver->id, code $driver->code ($driver->full_name)");

                        $driver->bea_id = $driverBEAId;
                        $driver->saveData();
                    } else {
                        $this->log("  Driver with bea_id $driverBEAId, code $driverBEA->CCO_CLAVECOND is not migrated yet");

                        $driver = new Driver();
                        $driver->bea_id = $driverBEA->CCO_IDCONDUCTOR;
                        $driver->first_name = $driverBEA->CCO_NOMBRE;
                        $driver->last_name = "$driverBEA->CCO_APELLIDOP $driverBEA->CCO_APELLIDOM";
                        $driver->identity = $driverBEA->CCO_CLAVECOND;
                        $driver->company_id = $this->company->id;
                        $driver->active = true;

                        if (!$driver->saveData()) {
                            $error = "Error on validation save DRIVER with id: $driverBEA->CCO_IDCONDUCTOR";
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
     * @param $vehicleBEAId
     * @param null $data
     * @param null $detectedVehicles
     * @return Vehicle|integer|null
     * @throws Exception
     */
    private function validateVehicle($vehicleBEAId, $data = null, &$detectedVehicles = null)
    {
        $onlyMigrate = $this->company->id == Company::MONTEBELLO ? collect(range(2453, 2467)) : null;

        $vehicle = null;
        if ($vehicleBEAId) {
            $vehicle = Vehicle::where('bea_id', $vehicleBEAId)
                ->where('db_id', $this->dbId)
                ->where('company_id', $this->company->id)->first();

            if (!$vehicle) {
                $vehicleBEA = $data ?: BEADB::for($this->company, $this->dbId)->select("SELECT * FROM C_AUTOBUS WHERE CAU_IDAUTOBUS = $vehicleBEAId")->first();

                if ($vehicleBEA) {
                    if ($onlyMigrate) {
                        if (!$onlyMigrate->contains(intval($vehicleBEA->CAU_NUMECONOM)) && intval($vehicleBEA->CAU_NUMECONOM) != 24610) {
                            return null;
                        }
                    }

                    $vehicle = $this->company->vehicles->filter(function (Vehicle $v) use ($vehicleBEA) {
                        return Str::upper(str_replace('-', '', $v->plate)) == Str::upper(str_replace('-', '', $vehicleBEA->CAU_PLACAS)) || Str::upper($v->number) == Str::upper($vehicleBEA->CAU_NUMECONOM);
                    })->first();

                    if ($vehicle) {
                        $duplicated = $detectedVehicles ? $detectedVehicles->get($vehicle->id) : null;

                        $routeDefault = $vehicle->dispatcherVehicle ? $vehicle->dispatcherVehicle->route->name : '';
                        $this->log("  Vehicle For bea_id = $vehicleBEAId, number $vehicleBEA->CAU_NUMECONOM and plate $vehicleBEA->CAU_PLACAS >> SYNC vehicle: id $vehicle->id, number $vehicle->number ($vehicle->plate) and Route = $routeDefault " . ($duplicated ? " ***** DUPLICATED *******" : ""));

                        $vehicle->bea_id = $vehicleBEAId;
                        $vehicle->save();

                        if ($detectedVehicles) {
                            $detectedVehicles->put($vehicle->id, $vehicle);
                        }
                    } else {
                        $this->log("  Vehicle with bea_id $vehicleBEAId, number $vehicleBEA->CAU_NUMECONOM and plate $vehicleBEA->CAU_PLACAS is not migrated yet");

                        $vehicle = new Vehicle();
                        $duplicatedPlates = BEADB::for($this->company, $this->dbId)->select("SELECT count(1) TOTAL FROM C_AUTOBUS WHERE CAU_PLACAS = '$vehicleBEA->CAU_PLACAS'")->first();

                        if ($duplicatedPlates->TOTAL > 1) $vehicleBEA->CAU_PLACAS = "$vehicleBEA->CAU_PLACAS-$vehicleBEA->CAU_NUMECONOM";

                        if ($vehicleBEA->CAU_PLACAS) {
                            $vehicle->id = Vehicle::max('id') + 1;
                            $vehicle->bea_id = $vehicleBEA->CAU_IDAUTOBUS;
                            $vehicle->plate = $vehicleBEA->CAU_PLACAS;
                            $vehicle->number = $vehicleBEA->CAU_NUMECONOM;
                            $vehicle->company_id = $this->company->id;
                            $vehicle->active = true;
                            $vehicle->in_repair = false;

                            $this->log("Migrated vehicle with bea_id = $vehicle->bea_id");

                            if (!$vehicle->save()) {
                                throw new Exception("Error saving VEHICLE with id: $vehicleBEA->CAU_IDAUTOBUS");
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