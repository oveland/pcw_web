<?php

namespace App\Services\BEA;

use App\Models\BEA\Commission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CommissionService
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
     * @return Builder[]|Collection|Commission[]
     */
    function all()
    {
        return Commission::with(['route', 'vehicle'])
            ->whereIn('route_id', $this->repository->getAllRoutes()->pluck('id'))
            ->whereIn('vehicle_id', $this->repository->getAllVehicles()->pluck('id'))
            ->get()->sortBy(function ($p) {
                return intval($p->vehicle->number);
            })->values();
    }
}