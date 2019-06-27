<?php

namespace App\Services\BEA;

use App\Models\BEA\Liquidation;
use App\Models\BEA\Mark;
use App\Models\BEA\Turn;
use App\Models\Vehicles\Vehicle;
use BEADB;
use Exception;
use Illuminate\Support\Collection;

class BEAService
{
    /**
     * @var DiscountService
     */
    public $discount;
    /**
     * @var BEARepository
     */
    public $repository;
    /**
     * @var CommissionService
     */
    private $commission;
    /**
     * @var PenaltyService
     */
    private $penalty;

    /**
     * BEAService constructor.
     * @param BEARepository $repository
     * @param DiscountService $discountService
     * @param CommissionService $commissionService
     * @param PenaltyService $penaltyService
     */
    public function __construct(BEARepository $repository, DiscountService $discountService, CommissionService $commissionService, PenaltyService $penaltyService)
    {
        $this->repository = $repository;
        $this->discount = $discountService;
        $this->commission = $commissionService;
        $this->penalty = $penaltyService;
    }

    /**
     * @return object
     * @throws Exception
     */
    function getLiquidationParams()
    {
        return (object)[
            'vehicles' => $this->repository->getAllVehicles(),
            'routes' => $this->repository->getAllRoutes(),
            //'discounts' => $this->discount->all(),
            'discounts' => [],
            'commissions' => $this->commission->all(),
            'trajectories' => $this->repository->getAllTrajectories(),
            'penalties' => $this->penalty->all()
        ];
    }

    /**
     * @param $vehicleId
     * @param $date
     * @return Collection
     */
    function getBEALiquidations($vehicleId, $date)
    {
        $beaLiquidations = collect([]);
        $liquidations = Liquidation::where('vehicle_id', $vehicleId)
            ->whereBetween('date', ["$date 00:00:00", "$date 23:59:59"])
            ->with(['user', 'marks', 'vehicle', 'marks.turn.vehicle', 'marks.turn.route', 'marks.turn.driver', 'marks.trajectory'])
            ->get();

        foreach ($liquidations as $liquidation) {
            $beaLiquidations->push((object)[
                'id' => $liquidation->id,
                'vehicle' => $liquidation->vehicle,
                'date' => $liquidation->date->toDateString(),
                'dateLiquidation' => $liquidation->created_at->toDateTimeString(),
                'liquidation' => $liquidation->liquidation,
                'totals' => $liquidation->totals,
                'user' => $liquidation->user,
                'marks' => $this->processResponseMarks($liquidation->marks),
            ]);
        }

        return $beaLiquidations;
    }

    /**
     * @param $vehicleId
     * @param $date
     * @return Collection
     */
    function getBEAMarks($vehicleId, $date)
    {
        $vehicle = Vehicle::find($vehicleId);
        if(!$vehicle)return collect([]);
        $vehicleTurns = Turn::where('vehicle_id', $vehicle->id)->get();
        $marks = Mark::whereIn('turn_id', $vehicleTurns->pluck('id'))
            ->where('liquidated', false)
            ->where('taken', false)
            ->whereBetween('date', ["$date 00:00:00", "$date 23:59:59"])
            ->with(['turn.vehicle', 'turn.route', 'turn.driver', 'trajectory'])
            ->get();

        return $this->processResponseMarks($marks);
    }

    /**
     * @param Collection $marks
     * @return Collection
     */
    private function processResponseMarks($marks)
    {
        return $marks->map(function (Mark $mark) {
            return $mark->getAPIFields();
        });
    }
}