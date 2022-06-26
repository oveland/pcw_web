<?php


namespace App\Services\LM;

use App\Models\Drivers\Driver;
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
    /**
     * @var integer
     */
    public $dbId;

    /**
     * @var Company|null
     */
    public $company;

    public function __construct(Company $company = null, $dbId = 1)
    {
        $this->company = $company;
        $this->dbId = $dbId;
    }

    public function forCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Vehicle[] | Collection
     */
    function getAllVehicles($onlyMigrated = false): Collection
    {
        $query = $this->company->activeVehicles()->where('db_id', $this->dbId);

        if ($onlyMigrated) {
            $query = $query
                ->where('bea_id', '>', 0);
        }

        return $query->get();
    }

    /**
     * @param $routeId
     * @return Trajectory[] | Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    function getTrajectoriesByRoute($routeId): Collection
    {
        return Trajectory::with('route')
            ->where('db_id', $this->dbId)
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
            ->where('db_id', $this->dbId)
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
        $routes = $this->company->activeRoutes
            ->where('db_id', $this->dbId)
            ->where('bea_id', '<>', null)
            ->values();
        return collect($routes);
    }

    /**
     * @return Driver[]
     */
    function getAllDrivers()
    {

        return $this->company->activeDrivers->where('db_id', $this->dbId);
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