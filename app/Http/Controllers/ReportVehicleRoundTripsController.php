<?php

namespace App\Http\Controllers;

use App\Exports\RouteRoundTripsExport;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use Illuminate\Http\Request;

class ReportVehicleRoundTripsController extends Controller
{

    /**
     * @var PCWAuthService
     */
    private $authService;
    /**
     * @var PCWExporterService
     */
    private $exporterService;

    public function __construct(PCWAuthService $authService, PCWExporterService $exporterService)
    {

        $this->authService = $authService;
        $this->exporterService = $exporterService;
    }

    /**
     * @return View
     */
    public function index()
    {
        $access = $this->authService->getAccessProperties();
        $companies = $access->companies;
        return view('reports.vehicles.round-trips.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return View | RouteRoundTripsExport
     */
    public function show(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $request->get('date-report');
        $routeReport = $request->get('route-report');

        $roundTripsReport = $this->buildRoundTripsReport($company, $dateReport, $routeReport);

        if ($request->get('export')) return $this->exporterService->exportRouteRoundTrips($roundTripsReport);

        return view('reports.vehicles.round-trips.show', compact(['roundTripsReport']));
    }

    /**
     * @param Company|null $company
     * @param $dateReport
     * @param string $routeReport
     * @return object
     */
    public function buildRoundTripsReport(Company $company = null, $dateReport, $routeReport = 'all')
    {
        $vehicles = $company->vehicles;

        $dispatchRegistersByVehicles = DispatchRegister::completed()
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->where('date', $dateReport);

        if ($routeReport != 'all') {
            $dispatchRegistersByVehicles = $dispatchRegistersByVehicles->where('route_id', $routeReport);
        }

        $dispatchRegistersByVehicles = $dispatchRegistersByVehicles->orderBy('id')
            ->get()
            ->groupBy('vehicle_id');


        $reports = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $vehicle = $vehicles->where('id', $vehicleId)->first();
            $dispatchRegistersByVehicleByRoutes = $dispatchRegistersByVehicle->groupBy('route_id');

            $reportRoundTripsByRoutes = collect([]);
            foreach ($dispatchRegistersByVehicleByRoutes as $routeId => $dispatchRegistersByVehicleByRoute) {
                $route = Route::find($routeId);
                $reportRoundTripsByRoutes->put(
                    $routeId,
                    (object)[
                        'route' => $route,
                        'dispatchRegisters' => $dispatchRegistersByVehicleByRoute,
                        'roundTripsByRoute' => $dispatchRegistersByVehicleByRoute->max('round_trip'),
                        'firstDepartureTime' => $dispatchRegistersByVehicleByRoute->min('departure_time'),
                        'lastArrivalTime' => $dispatchRegistersByVehicleByRoute->max('arrival_time')
                    ]
                );
            }

            $reports->put(
                $vehicleId,
                (object)[
                    'vehicle' => $vehicle,
                    'totalRoundTrips' => $reportRoundTripsByRoutes->sum('roundTripsByRoute'),
                    'reportRoundTripByRoutes' => $reportRoundTripsByRoutes->sortBy('lastArrivalTime'),
                ]
            );
        }

        $roundTripsReport = (object)[
            'company' => $company,
            'dateReport' => $dateReport,
            'routeReport' => $routeReport,
            'reports' => $reports->sortBy('totalRoundTrips'),
            'totalRoundTripsByFleet' => $reports->sum('totalRoundTrips')
        ];

        return $roundTripsReport;
    }
}
