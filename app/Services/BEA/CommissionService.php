<?php

namespace App\Services\BEA;

use App\Models\BEA\Commission;

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
        return Commission::with('route')->get();
    }
}