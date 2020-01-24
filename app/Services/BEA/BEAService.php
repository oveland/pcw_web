<?php

namespace App\Services\BEA;

use App\Models\BEA\Liquidation;
use App\Models\BEA\Mark;
use App\Models\BEA\Turn;
use App\Models\Company\Company;
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
     * @var BEASyncService
     */
    public $sync;

    /**
     * @var integer
     */
    private $companyID;

    /**
     * BEAService constructor.
     * @param BEASyncService $sync
     * @param BEARepository $repository
     * @param DiscountService $discountService
     * @param CommissionService $commissionService
     * @param PenaltyService $penaltyService
     */
    public function __construct(BEASyncService $sync, BEARepository $repository, DiscountService $discountService, CommissionService $commissionService, PenaltyService $penaltyService)
    {
        $this->repository = $repository;
        $this->discount = $discountService;
        $this->commission = $commissionService;
        $this->penalty = $penaltyService;
        $this->sync = $sync;
    }

    /**
     * @return object
     */
    function getLiquidationParams()
    {
        $vehicles = $this->repository->getAllVehicles();

        foreach ($vehicles as $vehicle){
            $this->sync->checkPenaltiesFor($vehicle);
        }

        return (object)[
            'vehicles' => $vehicles,
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
            ->whereDate('date', $date)
            ->with(['user', 'marks', 'vehicle', 'marks.turn.vehicle', 'marks.turn.route', 'marks.turn.driver', 'marks.trajectory'])
            ->get();

        foreach ($liquidations as $liquidation) {
            $beaLiquidations->push((object)[
                'id' => $liquidation->id,
                'vehicle' => $liquidation->vehicle,
                'date' => $liquidation->date->toDateString(),
                'liquidationUser' => $liquidation->user,
                'liquidationDate' => $liquidation->created_at->toDateTimeString(),
                'taken' => $liquidation->taken,
                'takingUser' => $liquidation->taken ? $liquidation->takingUser : null,
                'takingDate' => $liquidation->taken ? $liquidation->taking_date->toDateTimeString() : null,
                'liquidation' => $liquidation->liquidation,
                'totals' => $liquidation->totals,
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
    function getBEATakings($vehicleId, $date)
    {
        return $this->getBEALiquidations($vehicleId, $date)->where('taken', false);
    }

    /**
     * @param $vehicleId
     * @param $date
     * @return Collection
     */
    function getBEATakingsList($vehicleId, $date)
    {
        return $this->getBEALiquidations($vehicleId, $date)->where('taken', true);
    }

    /**
     * @param $vehicleId
     * @param $date
     * @return Collection
     */
    function getBEAMarks($vehicleId, $date)
    {
        $this->sync->for($vehicleId, $date)->last();

        $vehicle = Vehicle::find($vehicleId);
        $this->sync->checkDiscountsFor($vehicle);
        $this->sync->checkPenaltiesFor($vehicle);

        if (!$vehicle) return collect([]);
        $vehicleTurns = Turn::where('vehicle_id', $vehicle->id)->get();

        $marks = Mark::enabled()
            ->whereIn('turn_id', $vehicleTurns->pluck('id'))
            ->where('liquidated', false)
            ->where('taken', false)
            ->whereDate('date', $date)
            ->with(['turn.vehicle', 'turn.route', 'turn.driver', 'trajectory'])
            ->orderBy('initial_time')
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