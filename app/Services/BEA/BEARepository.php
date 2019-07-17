<?php


namespace App\Services\BEA;

use App\Models\BEA\DiscountType;
use App\Models\BEA\Trajectory;
use App\Models\Company\Company;
use App\Models\Drivers\Drivers;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;

class BEARepository
{
    public $company;

    public function __construct()
    {
        $this->company = Company::find(Company::COODETRANS);
    }

    /**
     * @return Vehicle[]
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
        return Trajectory::where('route_id', $routeId)->get();
    }

    /**
     * @return Trajectory[]
     */
    function getAllTrajectories()
    {
        $trajectories = collect([]);
        $trajectoriesDB = Trajectory::with('route')->get();
        foreach ($trajectoriesDB as $trajectory){
            $trajectories->push([
                'id' => $trajectory->id,
                'name' => $trajectory->name,
                'routeName' => $trajectory->route->name,
                'nameAndRoute' => $trajectory->route->name." | ".$trajectory->name,
                'description' => $trajectory->description,
                'route_id' => $trajectory->route_id,
            ]);
        }

        return $trajectories;
    }

    /**
     * @return Route[]
     */
    function getAllRoutes()
    {
        return $this->company->activeRoutes;
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
       return DiscountType::orderBy('name')->get();
    }
}