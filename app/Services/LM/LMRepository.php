<?php


namespace App\Services\LM;

use App\Models\LM\DiscountType;
use App\Models\LM\ManagementCost;
use App\Models\LM\Trajectory;
use App\Models\Company\Company;
use App\Models\Drivers\Drivers;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use function collect;

class LMRepository
{
    public $company;

    public function __construct(Company $company = null)
    {
        $this->company = $company;
    }

    public function forCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Vehicle[] | Collection
     */
    function getAllVehicles(): Collection
    {
        // return $this->company->activeVehicles()->where('bea_id', '>', 0)->get();
        return $this->company->activeVehicles()->get();
    }

    /**
     * @param $routeId
     * @return Trajectory[] | Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    function getTrajectoriesByRoute($routeId): Collection
    {
        return Trajectory::with('route')
            ->where('route_id', $routeId)
            ->whereIn('route_id', $this->getAllRoutes()->pluck('id'))
            ->get();
    }

    /**
     * @return Trajectory[] | Collection
     */
    function getAllTrajectories(): Collection
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
    function getAllRoutes(): Collection
    {
        return collect($this->company->activeRoutes->where('bea_id', '<>', null)->values());
    }

    /**
     * @return Drivers[]
     */
    function getAllDrivers()
    {
        return $this->company->activeDrivers;
    }

    /**
     * @return DiscountType[] | Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    function getAllDiscountTypes()
    {
        return DiscountType::where('company_id', $this->company->id)->orderBy('name')->get();
    }

    /**
     * @param Vehicle|null $vehicle
     * @return Collection | ManagementCost []
     */
    public function getManagementCosts(Vehicle $vehicle = null): Collection
    {
        $vehicles = $this->getAllVehicles();

        if ($vehicle) $vehicles = $vehicles->where('id', $vehicle->id);

        return ManagementCost::whereIn('vehicle_id', $vehicles->pluck('id'))->orderBy('uid')->get();
    }
}