<?php


namespace App\Services\BEA;


use App\Models\BEA\Commission;
use App\Models\BEA\Discount;
use App\Models\BEA\Liquidation;
use App\Models\BEA\Mark;
use App\Models\BEA\Penalty;
use App\Models\BEA\Turn;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class BEAService
{
    /**
     * @var DiscountService
     */
    private $discount;
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
        $vehicles = $this->repository->getAllVehicles();
        $routes = $this->repository->getAllRoutes();

        return (object)[
            'vehicles' => $vehicles,
            'routes' => $routes,
            'discounts' => $this->discount->all(),
            'commissions' => $this->commission->all(),
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
                'date' => $liquidation->date->toDateTimeString(),
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
     * @return Collection
     * @throws Exception
     */
    function getBEAMarks($vehicleId, $date)
    {
        $vehicleTurns = Turn::where('vehicle_id', $vehicleId)->get();
        $marks = Mark::whereIn('turn_id', $vehicleTurns->pluck('id'))
            ->where('liquidated', false)
            ->where('taken', false)
            ->whereBetween('date', ["$date 00:00:00", "$date 23:59:59"])
            ->with(['turn.vehicle', 'turn.route', 'turn.driver', 'trajectory'])
            ->get();

        return $this->processResponseMarks($marks);
    }

    /**
     * @param $marks
     * @return Collection
     */
    private function processResponseMarks($marks)
    {
        $beaMarks = collect([]);

        $allDiscounts = $this->discount->all();

        $allCommissions = $this->commission->all();
        $allPenalties = $this->penalty->all();

        foreach ($marks as $mark) {
            $duration = $mark->initialTime->diff($mark->finalTime);

            //---------------- RELATIONS FOR PENALTIES ---------------------
            $penaltyByRoute = $allPenalties->where('route_id', $mark->turn->route->id)->first();
            $penaltyValue = $mark->boarding * $penaltyByRoute->value;
            $penalty = (object)[
                'value' => $penaltyValue,
                'type' => $penaltyByRoute->type,
                'baseValue' => $penaltyByRoute->value,
            ];
            //------------------------------------------------------------------

            $beaMarks->push((object)[
                'id' => $mark->id,
                'turn' => $mark->turn,
                'date' => $mark->date->toDateString(),
                'initialTime' => $mark->initialTime->toTimeString(),
                'finalTime' => $mark->finalTime->toTimeString(),
                'duration' => $duration->h . "h " . $duration->i . " m",
                'trajectory' => $mark->trajectory,
                'passengersUp' => $mark->passengers_up,
                'passengersDown' => $mark->passengers_down,
                'locks' => $mark->locks,
                'auxiliaries' => $mark->auxiliaries,
                'boarded' => $mark->boarding,
                'imBeaMax' => $mark->im_bea_max,
                'imBeaMin' => $mark->im_bea_min,
                'totalBEA' => $mark->total_bea,
                'totalGrossBEA' => $mark->total_gross_bea,
                'passengersBEA' => $mark->passengers_bea,
                'discounts' => $mark->discounts->toArray(),
                'commission' => $mark->commission,
                'penalty' => $penalty,
                'status' => $mark->status,
                'liquidated' => $mark->liquidated,
                'liquidation_id' => $mark->liquidation_id,
                'taken' => $mark->taken,
            ]);
        }

        return $beaMarks;
    }
}