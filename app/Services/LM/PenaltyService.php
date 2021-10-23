<?php

namespace App\Services\LM;

use App\Models\LM\Penalty;
use App\Services\LM\LMRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PenaltyService
{
    /**
     * @var LMRepository
     */
    private $repository;

    public function __construct(LMRepository $repository)
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
            ->get()->sortBy(function ($p) {
                return intval($p->vehicle->number);
            })->values();
    }
}