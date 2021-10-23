<?php

namespace App\Services\LM;

use App\Models\LM\Builder;
use App\Models\LM\GlobalCosts;
use App\Services\LM\LMRepository;
use Illuminate\Database\Eloquent\Collection;

class CostsService
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
     * @return Builder[]|GlobalCosts[]|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    function globalCosts()
    {
        return GlobalCosts::whereCompany($this->repository->company)->get()->values();
    }
}