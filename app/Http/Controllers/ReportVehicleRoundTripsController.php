<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use App\Services\Exports\PCWExporterService;
use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
     * @return Factory|View
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
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $dateReport = $request->get('date-report');
        $routeReport = $request->get('route-report');
        $withEndDate = $request->get('with-end-date');
        $dateEndReport = $withEndDate ? $request->get('date-end-report') : null;
        $vehicleReport = $request->get('vehicle-report');

        $roundTripsReport = $this->buildRoundTripsReport($company, $dateReport, $dateEndReport, $routeReport, $vehicleReport, $withEndDate);

        if ($request->get('export')) $this->export($roundTripsReport);

        return view('reports.vehicles.round-trips.show', compact(['roundTripsReport']));
    }

    /**
     * @param Company $company
     * @param $dateReport
     * @param $dateEndReport
     * @param $routeReport
     * @param $vehicleId
     * @param $withEndDate
     * @return object
     */
    public function buildRoundTripsReport(Company $company, $dateReport, $dateEndReport, $routeReport, $vehicleReport, $withEndDate)
    {
        $dispatchRegisters = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($company, $dateReport, $dateEndReport, $routeReport, $vehicleReport)
            ->active()
            ->with('vehicle')
            ->orderBy('id')->get();

//        $dispatchRegistersByVehicles = DispatchRegister::completed()
//            ->whereIn('vehicle_id', $vehicles->pluck('id'))
//            ->where('date', $dateReport);
//
//        if ($routeReport != 'all') {
//            $dispatchRegistersByVehicles = $dispatchRegistersByVehicles->where('route_id', $routeReport);
//        }

        $dispatchRegisters = $dispatchRegisters->sortBy(function(DispatchRegister $dr){
            return $dr->vehicle->number;
        });

        $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id');


        $reports = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $vehicle = $dispatchRegistersByVehicle->first()->vehicle;
            $dispatchRegistersByVehicleByRoutes = $dispatchRegistersByVehicle->groupBy('route_id');

            $reportRoundTripsByRoutes = collect([]);
            foreach ($dispatchRegistersByVehicleByRoutes as $routeId => $dispatchRegistersByVehicleByRoute) {
                $route = Route::find($routeId);
                $reportRoundTripsByRoutes->put(
                    $routeId,
                    (object)[
                        'route' => $route,
                        'dispatchRegisters' => $dispatchRegistersByVehicleByRoute,
                        'roundTripsByRoute' => $dispatchRegistersByVehicleByRoute->count()
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
            'dateEndReport' => $dateEndReport,
            'vehicleReport' => $vehicleReport,
            'withEndDate' => $withEndDate,
            'reports' => $reports,
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
        $dateEndReport = $roundTripsReport->dateEndReport;
        $reports = $roundTripsReport->reports;
        $routeReport = $roundTripsReport->routeReport;
        $vehicle = $roundTripsReport->vehicleReport && $roundTripsReport->vehicleReport != 'all' ? Vehicle::find($roundTripsReport->vehicleReport) : null;

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

        $title = ($route ? "\n $route->name" : "");
        $title .= ($vehicle ? "\n ".__('Vehicle')." $vehicle->number" : "");

        $dateReport .= $dateEndReport ? " - $dateEndReport" : "";

        PCWExporterService::excel([
            'fileName' => __('Round trip report') . $roundTripsReport->dateReport,
            'title' => __('Round trip report') . $title,
            'subTitle' => $dateReport,
            'data' => $dataExcel,
            'type' => 'roundTripsVehicleReport'
        ]);
    }
}
