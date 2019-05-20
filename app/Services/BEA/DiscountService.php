<?php


namespace App\Services\BEA;


use App\Models\BEA\Discount;
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
     * @return Discount[]
     */
    function all()
    {
        return Discount::with(['vehicle', 'route', 'trajectory', 'discountType'])->get();
    }
}