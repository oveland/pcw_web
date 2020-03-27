<?php

namespace App\Services\BEA;

use App\Facades\BEADB;
use App\Models\BEA\Costs;
use App\Models\BEA\Discount;
use App\Models\BEA\ManagementCost;
use App\Models\BEA\Mark;
use App\Models\BEA\Penalty;
use App\Models\BEA\Trajectory;
use App\Models\BEA\Turn;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;

class BEASyncService
{
    /**
     * @var Vehicle
     */
    private $vehicle;
    /**
     * @var string
     */
    private $date;

    /**
     * @var Company $company
     */
    public $company;
    /**
     * @var BEARepository
     */
    private $repository;

    /**
     * BEASyncService constructor.
     * @param Company $company
     * @param BEARepository $repository
     */
    public function __construct(Company $company, BEARepository $repository)
    {
        $this->company = $company;
        $this->repository = $repository;
    }

    public function for($vehicleId, $date)
    {
        $this->vehicle = Vehicle::find($vehicleId);
        $this->date = $date;

        return $this;
    }

    /**
     * Sync last marks data
     */
    public function last()
    {
        try {
            //$this->turns();
            //$this->trajectories();
            $this->marks();

        } catch (Exception $e) {
            if ($this->vehicle && $this->date) DB::select("SELECT refresh_bea_marks_turns_numbers_function(" . $this->vehicle->id . ", '$this->date')");
        }
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
        $turns = BEADB::for($this->company)->select("SELECT * FROM A_TURNO WHERE ATR_IDTURNO > $lastIdMigrated");

        $maxSequence = collect(DB::select("SELECT max(id_crear_vehiculo) max FROM crear_vehiculo"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE vehicles_id_seq RESTART WITH $maxSequence");

        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_turns"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_turns_id_seq RESTART WITH $maxSequence");

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

        $vehicles = BEADB::for($this->company)->select("SELECT * FROM C_AUTOBUS");

        foreach ($vehicles as $vehicleBEA) {
            $this->validateVehicle($vehicleBEA->CAU_IDAUTOBUS, $vehicleBEA);
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
        $routes = BEADB::for($this->company)->select("SELECT * FROM C_RUTA");

        foreach ($routes as $routeBEA) {
            $this->validateRoute($routeBEA->CRU_IDRUTA, $routeBEA);
        }
    }

    /**
     * Sync A_DERROTERO >> bea_trajectories
     * @throws Exception
     */
    public function trajectories()
    {
        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_trajectories"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_trajectories_id_seq RESTART WITH $maxSequence");


        $trajectories = BEADB::for($this->company)->select("SELECT * FROM C_DERROTERO");

        foreach ($trajectories as $trajectoryBEA) {
            $this->validateTrajectory($trajectoryBEA->CDR_IDDERROTERO, $trajectoryBEA);
        }
    }

    /**
     * Sync A_MARCA >> bea_marks
     *
     * @throws Exception
     * @throws \Throwable
     */
    public function marks()
    {
        $maxSequence = collect(DB::select("SELECT max(id) max FROM bea_marks"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE bea_marks_id_seq RESTART WITH $maxSequence");

        $queryVehicle = $this->vehicle ? "AND AMR_IDTURNO IN (SELECT ATR_IDTURNO FROM A_TURNO WHERE ATR_IDAUTOBUS = " . ($this->vehicle->bea_id ?? 0) . ")" : "";

        $marks = BEADB::for($this->company)->select("SELECT * FROM A_MARCA WHERE (AMR_FHINICIO > " . ($this->date ? "'$this->date'" : 'current_date - 30') . ") $queryVehicle");

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

            $duplicatedIdsPCW = $marksIdsPCW->diff($markIdsBEA)->implode(',');
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

        // Synchronized models
        $turn = $this->validateTurn($markBEA->AMR_IDTURNO);
        $trajectory = $this->validateTrajectory($markBEA->AMR_IDDERROTERO);

        $mark->company_id = $this->company->id;
        $mark->bea_id = $markBEA->AMR_IDMARCA;
        $mark->turn_id = $turn->id;
        $mark->trajectory_id = $trajectory ? $trajectory->id : null;
        $mark->date = Carbon::createFromFormat("Y-m-d H:i:s", $markBEA->AMR_FHINICIO)->format(config('app.simple_date_time_format'));
        $mark->initial_time = Carbon::createFromFormat("Y-m-d H:i:s", $markBEA->AMR_FHINICIO)->format(config('app.simple_time_format'));
        $mark->final_time = Carbon::createFromFormat("Y-m-d H:i:s", $markBEA->AMR_FHFINAL)->format(config('app.simple_time_format'));
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

        if (!$turn && $turnBEAId) {
            $turnBEA = $data ? $data : BEADB::for($this->company)->select("SELECT * FROM A_TURNO WHERE ATR_IDTURNO = $turnBEAId")->first();
            if ($turnBEA) {
                $route = $this->validateRoute($turnBEA->ATR_IDRUTA);
                $driver = $this->validateDriver($turnBEA->ATR_IDCONDUCTOR);
                $vehicle = $this->validateVehicle($turnBEA->ATR_IDAUTOBUS);

                $turn = new Turn();
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
            $trajectoryBEA = $data ? $data : BEADB::for($this->company)->select("SELECT * FROM C_DERROTERO WHERE CDR_IDDERROTERO = $trajectoryBEAId")->first();

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
        $route = Route::where('bea_id', $routeBEAId)->where('company_id', $this->company->id)->first();

        if (!$route && $routeBEAId) {
            $routeBEA = $data ? $data : BEADB::for($this->company)->select("SELECT * FROM C_RUTA WHERE CRU_IDRUTA = $routeBEAId")->first();
            if ($routeBEA) {
                $route = new Route();
                $route->bea_id = $routeBEA->CRU_IDRUTA;
                $route->name = $routeBEA->CRU_DESCRIPCION;
                $route->distance = 0;
                $route->road_time = 0;
                $route->url = 'none';
                $route->company_id = $this->company->id;
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
     * @param $driverBEAId
     * @param $data
     * @return Driver|Model|null
     * @throws Exception
     */
    private function validateDriver($driverBEAId, $data = null)
    {
        $driver = Driver::where('bea_id', $driverBEAId)->where('company_id', $this->company->id)->first();

        if (!$driver && $driverBEAId) {
            $driverBEA = $data ? $data : BEADB::for($this->company)->select("SELECT * FROM C_CONDUCTOR WHERE CCO_IDCONDUCTOR = $driverBEAId")->first();
            if ($driverBEA) {
                $driver = new Driver();
                $driver->bea_id = $driverBEA->CCO_IDCONDUCTOR;
                $driver->first_name = $driverBEA->CCO_NOMBRE;
                $driver->last_name = "$driverBEA->CCO_APELLIDOP $driverBEA->CCO_APELLIDOM";
                $driver->identity = $driverBEA->CCO_CLAVECOND;
                $driver->company_id = $this->company->id;
                $driver->active = true;

                if (!$driver->saveData()) {
                    throw new Exception("Error on validation save DRIVER with id: $driverBEA->CCO_IDCONDUCTOR");
                }
            }
        }

        return $driver;
    }

    /**
     * @param $vehicleBEAId
     * @param null $data
     * @return Vehicle|integer|null
     * @throws Exception
     */
    private function validateVehicle($vehicleBEAId, $data = null)
    {
        $vehicle = Vehicle::where('bea_id', $vehicleBEAId)->where('company_id', $this->company->id)->first();

        if (!$vehicle && $vehicleBEAId) {
            $vehicleBEA = $data ? $data : BEADB::for($this->company)->select("SELECT * FROM C_AUTOBUS WHERE CAU_IDAUTOBUS = $vehicleBEAId")->first();

            if ($vehicleBEA) {
                $vehicle = new Vehicle();
                $duplicatedPlates = BEADB::for($this->company)->select("SELECT count(1) TOTAL FROM C_AUTOBUS WHERE CAU_PLACAS = '$vehicleBEA->CAU_PLACAS'")->first();

                if ($duplicatedPlates->TOTAL > 1) $vehicleBEA->CAU_PLACAS = "$vehicleBEA->CAU_PLACAS-$vehicleBEA->CAU_NUMECONOM";

                if ($vehicleBEA->CAU_PLACAS) {
                    $vehicle->id = Vehicle::max('id') + 1;
                    $vehicle->bea_id = $vehicleBEA->CAU_IDAUTOBUS;
                    $vehicle->plate = $vehicleBEA->CAU_PLACAS;
                    $vehicle->number = $vehicleBEA->CAU_NUMECONOM;
                    $vehicle->company_id = $this->company->id;
                    $vehicle->active = true;
                    $vehicle->in_repair = false;

                    if (!$vehicle->save()) {
                        throw new Exception("Error saving VEHICLE with id: $vehicleBEA->CAU_IDAUTOBUS");
                    } else {
                        $this->checkVehicleParams($vehicle);
                    }
                }


            }
        }

        return $vehicle;
    }

    private function checkVehicleParams(Vehicle $vehicle)
    {
        if ($vehicle) {
            $this->checkDiscountsFor($vehicle);

            $penalties = Penalty::where('vehicle_id', $referenceVehicle->id)->get();
            foreach ($penalties as $penalty) {
                $exists = Penalty::where('vehicle_id', $vehicle->id)->where('route_id', $penalty->route->id)->first();

                if (!$exists) {
                    $new = new Penalty();
                    $new->vehicle_id = $vehicle->id;
                    $new->route_id = $penalty->route_id;
                    $new->type = $penalty->type;
                    $new->value = $penalty->value;

                    if (!$new->save()) $ok = false;
                }
            }
        }
    }

    public function checkPenaltiesFor(Vehicle $vehicle)
    {
        $routes = $this->repository->getAllRoutes();

        $criteria = [
            0 => (object)[
                'type' => 'boarding',
                'value' => 3000,
            ]
        ];

        foreach ($routes as $route) {
            $c = $criteria[0];

            $exists = Penalty::where('vehicle_id', $vehicle->id)->where('route_id', $route->id)->first();

            if (!$exists) {
                Penalty::create([
                    'vehicle_id' => $vehicle->id,
                    'route_id' => $route->id,
                    'type' => $c->type,
                    'value' => $c->value,
                ]);
            }
        }
    }

    public function checkManagementCostsFor(Vehicle $vehicle)
    {
        $vehicleCosts = $vehicle->costsBEA;
        $allCosts = Costs::whereCompany($vehicle->company)->get();

        foreach ($allCosts as $cost) {
            if (($cost->uid == ManagementCost::PAYROLL_ID && $vehicle->company->configBEA('withPayRoll') || $cost->uid != ManagementCost::PAYROLL_ID)) {
                $exists = $vehicleCosts->where('uid', $cost->uid)->first();
                if (!$exists) {
                    ManagementCost::create([
                        'uid' => $cost->uid,
                        'vehicle_id' => $vehicle->id,
                        'name' => $cost->name,
                        'concept' => $cost->concept,
                        'description' => $cost->description,
                        'value' => $cost->value,
                    ]);
                }
            }
        }
    }

    public function checkDiscountsFor(Vehicle $vehicle)
    {
        $routes = $this->repository->getAllRoutes();
        $discountTypes = $this->repository->getAllDiscountTypes();

        foreach ($routes as $route) {
            $trajectories = $this->repository->getTrajectoriesByRoute($route->id);
            foreach ($trajectories as $trajectory) {
                foreach ($discountTypes as $discountType) {
                    $exists = Discount::where('discount_type_id', $discountType->id)
                        ->where('vehicle_id', $vehicle->id)
                        ->where('route_id', $route->id)
                        ->where('trajectory_id', $trajectory->id)
                        ->first();

                    if (!$exists) {
                        Discount::create([
                            'discount_type_id' => $discountType->id,
                            'vehicle_id' => $vehicle->id,
                            'route_id' => $route->id,
                            'trajectory_id' => $trajectory->id,
                            'value' => $discountType->default
                        ]);
                    }
                }
            }
        }
    }
}