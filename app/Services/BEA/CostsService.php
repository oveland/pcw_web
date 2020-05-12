<?php

namespace App\Services\BEA;

use App\Models\BEA\Builder;
use App\Models\BEA\GlobalCosts;
use Illuminate\Database\Eloquent\Collection;

class CostsService
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
     * @return Builder[]|GlobalCosts[]|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    function globalCosts()
    {
        return GlobalCosts::whereCompany($this->repository->company)->get()->values();
    }
}