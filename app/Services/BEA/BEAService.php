<?php

namespace App\Services\BEA;

use App\Models\BEA\Liquidation;
use App\Models\BEA\Mark;
use App\Models\BEA\Turn;
use App\Models\Vehicles\Vehicle;
use App\Services\BEA\Reports\BEAReportService;
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
     * @var BEAReportService
     */
    private $report;

    /**
     * BEAService constructor.
     * @param BEASyncService $sync
     * @param BEAReportService $report
     * @param BEARepository $repository
     * @param DiscountService $discountService
     * @param CommissionService $commissionService
     * @param PenaltyService $penaltyService
     */
    public function __construct(BEASyncService $sync, BEAReportService $report, BEARepository $repository, DiscountService $discountService, CommissionService $commissionService, PenaltyService $penaltyService)
    {
        $this->repository = $repository;
        $this->discount = $discountService;
        $this->commission = $commissionService;
        $this->penalty = $penaltyService;
        $this->sync = $sync;
        $this->report = $report;
    }

    /**
     * @param false $api
     * @return object
     */
    function getLiquidationParams($api = false)
    {
        $vehicles = $this->repository->getAllVehicles();

        foreach ($vehicles as $vehicle) {
            $this->sync->checkCommissionsFor($vehicle);
            $this->sync->checkPenaltiesFor($vehicle);
            $this->sync->checkManagementCostsFor($vehicle);
        }

        return (object)[
            'vehicles' => $vehicles,
            'routes' => $this->repository->getAllRoutes(),
            //'discounts' => $this->discount->all(),
            'discounts' => [],
            'commissions' => $this->commission->all(),
            'trajectories' => $this->repository->getAllTrajectories(),
            'penalties' => $this->penalty->all(),
            'managementCosts' => $this->repository->getManagementCosts()
        ];
    }

    /**
     * @param $vehicleId
     * @param $date
     * @param null $finalDate
     * @param null $driverId
     * @return Collection
     */
    function getBEALiquidations($vehicleId, $date, $finalDate = null, $driverId = null)
    {
        $finalDate = $finalDate ?? $date;

        $beaLiquidations = collect([]);
        $liquidations = Liquidation::where('vehicle_id', $vehicleId)
            ->whereBetween('date', ["$date", "$finalDate 23:59:59"])
            ->with(['user', 'marks', 'vehicle', 'marks.turn.vehicle', 'marks.turn.route', 'marks.turn.driver', 'marks.trajectory'])
            ->orderBy('date')
            ->get();

        if ($driverId) {
            $liquidations = $liquidations->filter(function (Liquidation $liquidation) use ($driverId) {
                return $liquidation->marks->filter(function (Mark $mark) use ($driverId) {
                    return $mark->turn->driver_id && $mark->turn->driver_id == $driverId;
                })->count();
            });
        }

        $prevLiquidation = Liquidation::where('vehicle_id', $vehicleId)
            ->where('date', '<', $date)
            ->orderByDesc('date')
            ->limit(1)
            ->first();

        if ($driverId && $prevLiquidation) {
            $prevLiquidation = $prevLiquidation->marks->filter(function (Mark $mark) use ($driverId) {
                return $mark->turn->driver_id == $driverId;
            });
        }

        foreach ($liquidations as $liquidation) {
            if ($liquidation->marks->isNotEmpty()) {
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
                    'prevLiquidation' => $prevLiquidation->liquidation ?? null,
                    'totals' => $liquidation->totals,
                    'marks' => $this->processResponseMarks($liquidation->marks),
                ]);
            }
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
    function getDailyReport($vehicleId, $date)
    {
        $beaLiquidations = $this->getBEALiquidations($vehicleId, $date)->where('taken', true);
        return $this->report->buildDailyReport($beaLiquidations);
    }

    /**
     * @param $vehicleId
     * @param $driverId
     * @param $initialDate
     * @param $finalDate
     * @return Collection
     */
    function getMainReport($vehicleId, $driverId, $initialDate, $finalDate)
    {
        $beaLiquidations = $this->getBEALiquidations($vehicleId, $initialDate, $finalDate, $driverId)->where('taken', true);
        return $this->report->buildMainReport($beaLiquidations);
    }

    /**
     * @param $date
     * @return object
     */
    function getConsolidatedDailyReport($date)
    {
        return $this->report->buildConsolidatedDailyReport($this->repository->company, $date);
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
        $this->sync->checkCommissionsFor($vehicle);
        $this->sync->checkPenaltiesFor($vehicle);
        $this->sync->checkManagementCostsFor($vehicle);

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
     * @param Collection|array $marks
     * @return Collection
     */
    private function processResponseMarks($marks)
    {
        return $marks->map(function (Mark $mark) {
            return $mark->getAPIFields();
        });
    }
}