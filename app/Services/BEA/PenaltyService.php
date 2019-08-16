<?php

namespace App\Services\BEA;

use App\Models\BEA\Penalty;

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
     * @return Penalty[]|
     */
    function all()
    {
        return Penalty::with('route')->get();
    }
}