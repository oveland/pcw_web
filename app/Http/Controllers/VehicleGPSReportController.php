<?php

namespace App\Http\Controllers;

use App\Models\Vehicles\LastLocation;
use App\Models\Company\Company;
use App\Models\Vehicles\VehicleStatus;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleStatusReport;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWTime;
use App\Services\Reports\Routes\RouteService;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VehicleGPSReportController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $authService;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $authService
     */
    public function __construct(PCWAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $access = $this->authService->access();
        $companies = $access->companies;
        $vehicles = $access->vehicles;

        return view('reports.vehicles.gps.index', compact(['companies', 'vehicles']));
    }

    public function searchReport(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $onlyVehiclesActive = $request->get('active-vehicles');
        $excludeVehiclesInRepair = $request->get('exclude-in-repair');

        $thresholdTotalLocationsForOK = $request->get('minimum-locations-daily');
        $thresholdPercentForOK = $request->get('minimum-percent-for-OK');

        $initialDate = Carbon::createFromFormat('Y-m-d', $request->get('initial-date'))->subDays(1);
        $finalDate = Carbon::createFromFormat('Y-m-d', $request->get('final-date'));

        if ($initialDate->greaterThan($finalDate)) {
            return view('partials.dates.invalidRange');
        } else if (Carbon::now()->subDays(60)->greaterThanOrEqualTo($initialDate)) {
            dd(__("The initial date cannot be lower than 60 days past from current"));
        } else if ($finalDate->isToday() || $finalDate->greaterThan(Carbon::now())) {
            dd(__("The final date cannot be greater or equals than current"));
        }

        $dateRange = collect(PCWTime::dateRange($initialDate, $finalDate));

        $vehicles = $company->vehicles;
        $vehicles = $onlyVehiclesActive ? $vehicles->where('active', true) : $vehicles;
        $vehicles = $excludeVehiclesInRepair ? $vehicles->where('in_repair', false) : $vehicles;

        $lastLocationsAll = LastLocation::whereBetween('date', [$dateRange->first()->toDateString(). " 00:00:00", $dateRange->last()->toDateString() . " 23:59:59"])
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->get();

        $lastLocationsVehicles = $lastLocationsAll->groupBy('vehicle_id');

        $reportByVehicles = collect([]);

        foreach ($vehicles as $vehicle){
            $reportVehicle = collect([]);

            $vehicleId = $vehicle->id;
            $lastLocations = isset($lastLocationsVehicles[$vehicleId]) ? $lastLocationsVehicles[$vehicleId]->sortBy('date') : null;

            $last = null;
            $index = 0;

            foreach ($dateRange as $date) {
                $lastLocation = $lastLocations ? $lastLocations->filter(function($ll) use ($date){ return $ll->date->toDateString() == $date->toDateString(); })->first() : null;

                $totalLocations = $lastLocation && $last ? ($lastLocation->total_locations - $last->total_locations) : 0;

                if ($date->lessThan(Carbon::createFromFormat('Y-m-d', '2019-19-26'))) {
                    $totalLocations = intval($totalLocations / 2);
                }

                $totalKm = $lastLocation ? $lastLocation->current_mileage : 0;

                if ($index > 0) {
                    $reportVehicle->push((object)[
                        'isOK' => $totalLocations >= $thresholdTotalLocationsForOK,
                        'date' => $date,
                        'totalLocations' => $totalLocations,
                        'totalKm' => $totalKm,
                        'hasLocations' => !!$lastLocation
                    ]);
                }

                if ($lastLocation) $last = $lastLocation;
                $index++;
            }

            $percentOK = 100 * $reportVehicle->where('isOK', true)->count() / $reportVehicle->count();
            $reportByVehicle = (object)[
                'isOK' => $percentOK >= $thresholdPercentForOK,
                'percentOK' => $percentOK,
                'report' => $reportVehicle,
                'reportNR' => $reportVehicle->where('totalLocations', '<', $thresholdTotalLocationsForOK),
                'averageLocations' => $reportVehicle->average('totalLocations'),
                'averageKm' => $reportVehicle->average('totalKm')
            ];

            $reportByVehicles->put($vehicleId, $reportByVehicle);
        }

        $reportByVehicles = $reportByVehicles->sortByDesc('percentOK')->sortByDesc('isOK');

        $gpsOK = $reportByVehicles->where('isOK', true)->count();
        $gpsBAD = $reportByVehicles->where('isOK', false)->count();

        $statistics = (object)[
            'gpsOK' => $gpsOK,
            'gpsBAD' => $gpsBAD,
            'percentOK' => 100 * $gpsOK / $reportByVehicles->count(),
            'percentBAD' => 100 * $gpsBAD / $reportByVehicles->count(),
            'averageLocations' => number_format($reportByVehicles->average('averageLocations'), 0, '', ''),
            'averageKm' => number_format($reportByVehicles->average('averageKm') / 1000, 0, '', '')
        ];

        return view('reports.vehicles.gps.report', compact(['reportByVehicles', 'statistics']));
    }
}
