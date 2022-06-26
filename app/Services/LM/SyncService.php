<?php

namespace App\Services\LM;

use App\Models\LM\Commission;
use App\Models\LM\Discount;
use App\Models\LM\DiscountType;
use App\Models\LM\GlobalCosts;
use App\Models\LM\ManagementCost;
use App\Models\LM\Penalty;
use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Log;

abstract class SyncService
{
    protected $type;

    /**
     * @var Company $company
     */
    public $company;

    /**
     * @var LMRepository
     */
    protected $repository;

    /**
     * @var Vehicle
     */
    protected $vehicle;

    /**
     * @var string
     */
    protected $date;

    /**
     * @var int
     */
    protected $dbId = 1;

    /**
     * BEASyncService constructor.
     * @param Company $company
     * @param LMRepository $repository
     */
    public function __construct(Company $company, LMRepository $repository)
    {
        $this->company = $company;
        $this->repository = $repository;
    }

    public function for($vehicleId, $date, $dbId = 1)
    {
        $this->vehicle = Vehicle::find($vehicleId);
        $this->date = $date;
        $this->dbId = $dbId ?? 1;

        return $this;
    }

    abstract function turns();

    abstract function vehicles();

    abstract function drivers();

    abstract function routes();

    abstract function trajectories();

    abstract function marks();

    abstract function locations(Vehicle $vehicle, $date);

    /**
     * Sync last marks data
     */
    public function last()
    {
        try {
            if (config('app.env') == 'beta' || true) {
//                $this->turns();
                //$this->trajectories();

//                $this->vehicles();

                $this->marks();
            }
        } catch (Exception $e) {
            if ($this->vehicle && $this->date) DB::select("SELECT refresh_bea_marks_turns_numbers_function(" . $this->vehicle->id . ", '$this->date')");

            throw new Exception('Error LM sync last data! • ' . $e->getMessage());
        }
    }

    function checkVehicleParams(Vehicle $vehicle)
    {
        $this->log("        Checking all params for vehicle $vehicle->number");
        $this->discountTypes();
        $this->checkDiscountsFor($vehicle);
        $this->checkCommissionsFor($vehicle);
        $this->checkPenaltiesFor($vehicle);
        $this->checkManagementCostsFor($vehicle);
    }

    public function checkCommissionsFor(Vehicle $vehicle)
    {
        $routes = $this->repository->getAllRoutes();

        foreach ($routes as $route) {
            $exists = Commission::where('vehicle_id', $vehicle->id)->where('route_id', $route->id)->first();

            if (!$exists) {
                Commission::create([
                    'vehicle_id' => $vehicle->id,
                    'route_id' => $route->id,
                    'type' => 'percent',
                    'value' => 0,
                ]);
            }
        }
    }

    public function checkPenaltiesFor(Vehicle $vehicle)
    {
        $routes = $this->repository->getAllRoutes();

        foreach ($routes as $route) {
            $exists = Penalty::where('vehicle_id', $vehicle->id)->where('route_id', $route->id)->first();

            if (!$exists) {
                Penalty::create([
                    'vehicle_id' => $vehicle->id,
                    'route_id' => $route->id,
                    'type' => 'boarding',
                    'value' => 0,
                ]);
            }
        }
    }

    public function checkManagementCostsFor(Vehicle $vehicle)
    {
        $vehicleCosts = $vehicle->costsBEA;
        $globalCosts = GlobalCosts::whereCompany($vehicle->company)->get();

        foreach ($globalCosts as $cost) {
            $exists = $vehicleCosts->where('uid', $cost->uid)->first();
            if (!$exists) {
                ManagementCost::create([
                    'uid' => $cost->uid,
                    'vehicle_id' => $vehicle->id,
                    'name' => $cost->name,
                    'concept' => $cost->concept,
                    'description' => $cost->description,
                    'value' => $cost->value,
                    'priority' => $cost->priority,
                    'global' => true,
                ]);
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
                            'value' => $discountType->default,
                            'required' => $discountType->required,
                            'optional' => $discountType->optional,
                        ]);
                    }
                }
            }
        }
    }

    public function discountTypes()
    {
        $types = [
            'Mobility auxilio' => (object)[
                'uid' => 1,
                'icon' => 'fa fa-user text-warning',
                'default' => 0,
                'required' => true,
                'optional' => false,
            ],
            'Fuel' => (object)[
                'uid' => 2,
                'icon' => 'fa fa-tachometer',
                'default' => 0,
                'required' => true,
                'optional' => false,
            ],
            'Operative Expenses' => (object)[
                'uid' => 3,
                'icon' => 'fa fa-hint text-warning',
                'default' => 0,
                'required' => true,
                'optional' => false,
            ],
            'Tolls' => (object)[
                'uid' => 4,
                'icon' => 'fa fa-ticket',
                'default' => 0,
                'required' => true,
                'optional' => false,
            ],
            'Provisions' => (object)[
                'uid' => 5,
                'icon' => 'fa fa-money',
                'default' => 0,
                'required' => false,
                'optional' => true,
            ],
        ];

        foreach ($types as $name => $type) {
            $exists = DiscountType::where('company_id', $this->company->id)->where('uid', $type->uid)->first();

            if (!$exists) {
                DiscountType::create([
                    'uid' => $type->uid,
                    'name' => __(ucfirst($name)),
                    'icon' => $type->icon,
                    'description' => __('Discount by') . " " . __(ucfirst($name)),
                    'default' => $type->default,
                    'company_id' => $this->company->id,
                    'required' => $type->required,
                    'optional' => $type->optional,
                ]);
            }
            /*else {
                $exists->required = $type->required;
                $exists->optional = $type->optional;
                $exists->save();
            }*/
        }
    }

    protected function log($message)
    {
        $now = Carbon::now();
        echo "$now • $message";
        Log::channel('lm')->info($this->company->short_name . " • $this->type • " . $message);
    }
}