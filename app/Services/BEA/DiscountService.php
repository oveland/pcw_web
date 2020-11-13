<?php


namespace App\Services\BEA;


use App\Models\BEA\Discount;
use Illuminate\Database\Eloquent\Builder;
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
     * @param $trajectoryId
     * @param bool $api
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|Discount[]
     */
    function byVehicleAndTrajectory($vehicleId, $trajectoryId, $api = false)
    {
        $discounts = Discount::with(['vehicle', 'route', 'trajectory', 'discountType'])
            ->where('vehicle_id', $vehicleId)
            ->where('trajectory_id', $trajectoryId)
            ->get();

        return $this->response($discounts, $api);
    }

    /**
     * @param $vehicleId
     * @param $routeId
     * @param $discountTypeId
     * @param bool $api
     * @return array|Collection
     */
    function byVehicleAndRouteAndType($vehicleId, $routeId, $discountTypeId, $api = false)
    {
        $discounts = Discount::with(['vehicle', 'route', 'trajectory', 'discountType'])
            ->where('vehicle_id', $vehicleId)
            ->where('route_id', $routeId)
            ->where('discount_type_id', $discountTypeId)
            ->get()->first();

        return $this->response($discounts, $api);
    }

    /**
     * @param Collection|array $discounts
     * @param bool $api
     * @return array
     */
    private function response($discounts, $api = false)
    {
        if (!$api) return $discounts;
        return $discounts->map(function (Discount $discount) {
            return $discount->getAPIFields();
        })->toArray();
    }
}