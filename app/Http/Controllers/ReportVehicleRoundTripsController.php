<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use App\Services\PCWExporterService;
use Auth;
use Illuminate\Http\Request;

class ReportVehicleRoundTripsController extends Controller
{

    /**
     * @var GeneralController
     */
    private $generalController;

    public function __construct(GeneralController $generalController)
    {
        $this->generalController = $generalController;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.vehicles.round-trips.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $dateReport = $request->get('date-report');
        $routeReport = $request->get('route-report');

        $roundTripsReport = $this->buildRoundTripsReport($company, $dateReport, $routeReport);

        if ($request->get('export')) $this->export($roundTripsReport);

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

    /**
     * @param $roundTripsReport
     */
    public function export($roundTripsReport)
    {
        $dateReport = $roundTripsReport->dateReport;
        $reports = $roundTripsReport->reports;
        $routeReport = $roundTripsReport->routeReport;

        $dataExcel = array();
        foreach ($reports as $report) {
            $vehicle = $report->vehicle;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Round trips') => intval($report->totalRoundTrips),                  # D CELL
            ];;
        }

        $route = $routeReport != 'all' ? Route::find($routeReport) : null;

        $titleRoute = ($route ? "\n $route->name" : "");

        PCWExporterService::excel([
            'fileName' => __('Round trip report') . " $dateReport",
            'title' => __('Round trip report').$titleRoute,
            'subTitle' => $dateReport,
            'data' => $dataExcel,
            'type' => 'roundTripsVehicleReport'
        ]);
    }
}
