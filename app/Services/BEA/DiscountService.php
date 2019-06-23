<?php


namespace App\Services\BEA;


use App\Models\BEA\Discount;
use App\Models\Vehicles\Vehicle;
use Exception;
use Illuminate\Support\Collection;

class DiscountService
{
    /**
     * @var BEARepository
     */
    private $repository;

    public function __construct(BEARepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Discount[] | Collection
     */
    function all()
    {
        return Discount::with(['vehicle', 'route', 'trajectory', 'discountType'])->get();
    }

    /**
     * @param $vehicleId
     * @param $routeId
     * @param $trajectoryId
     * @return Discount[]
     */
    function byVehicleAndRouteAndTrajectory($vehicleId, $routeId, $trajectoryId)
    {
        return Discount::with(['vehicle', 'route', 'trajectory', 'discountType'])
            ->where('vehicle_id', $vehicleId)
            ->where('route_id', $routeId)
            ->where('trajectory_id', $trajectoryId)
            ->get();
    }
}