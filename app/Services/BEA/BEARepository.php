<?php


namespace App\Services\BEA;

use App\Models\BEA\DiscountType;
use App\Models\BEA\ManagementCost;
use App\Models\BEA\Trajectory;
use App\Models\Company\Company;
use App\Models\Drivers\Drivers;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Collection;

class BEARepository
{
    public $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Vehicle[] | Collection
     */
    function getAllVehicles()
    {
        return $this->company->activeVehicles;
    }

    /**
     * @param $routeId
     * @return Trajectory[]
     */
    function getTrajectoriesByRoute($routeId)
    {
        return Trajectory::with('route')
            ->where('route_id', $routeId)
            ->whereIn('route_id', $this->getAllRoutes()->pluck('id'))
            ->get();
    }

    /**
     * @return Trajectory[]
     */
    function getAllTrajectories()
    {
        $trajectories = collect([]);

        $trajectoriesDB = Trajectory::with('route')
            ->whereIn('route_id', $this->getAllRoutes()->pluck('id'))
            ->get();
        foreach ($trajectoriesDB as $trajectory) {
            $trajectories->push([
                'id' => $trajectory->id,
                'name' => $trajectory->name,
                'routeName' => $trajectory->route->name,
                'nameAndRoute' => $trajectory->route->name . " | " . $trajectory->name,
                'description' => $trajectory->description,
                'route_id' => $trajectory->route_id,
            ]);
        }

        return $trajectories;
    }

    /**
     * @return Route[] | Collection
     */
    function getAllRoutes()
    {
        return collect($this->company->activeRoutes->where('bea_id', '>', 0)->values());
    }

    /**
     * @return Drivers[]
     */
    function getAllDrivers()
    {
        return $this->company->activeDrivers;
    }

    /**
     * @return DiscountType[]
     */
    function getAllDiscountTypes()
    {
        return DiscountType::where('company_id', $this->company->id)->orderBy('name')->get();
    }

    /**
     * @param Vehicle|null $vehicle
     * @return ManagementCost[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getManagementCosts(Vehicle $vehicle = null)
    {
        $vehicles = $this->getAllVehicles();

        if ($vehicle) $vehicles = $vehicles->where('id', $vehicle->id);

        return ManagementCost::whereIn('vehicle_id', $vehicles->pluck('id'))->orderBy('uid')->get();
    }
}