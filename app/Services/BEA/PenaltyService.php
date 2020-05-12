<?php

namespace App\Services\BEA;

use App\Models\BEA\Penalty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PenaltyService
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
     * @return Penalty[]|Builder[]|Collection
     */
    function all()
    {
        return Penalty::with(['route', 'vehicle'])
            ->whereIn('route_id', $this->repository->getAllRoutes()->pluck('id'))
            ->whereIn('vehicle_id', $this->repository->getAllVehicles()->pluck('id'))
            ->get()->sortBy(function ($p){
            return intval($p->vehicle->number);
        })->values();
    }
}