<?php

namespace App\Services\BEA;

use App\Models\BEA\Commission;
use App\Models\Company\Company;

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
     * @return Commission[]
     */
    function all()
    {
        return Commission::with('route')
            ->whereIn('route_id', $this->repository->getAllRoutes()->pluck('id'))
            ->get();
    }
}