<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Http\Controllers\Utils\Database;
use App\LocationReport;
use App\Route;
use App\Services\PCWExporter;
use App\Vehicle;
use Auth;
use Excel;
use Illuminate\Http\Request;

class ReportVehicleRoundTripsController extends Controller
{
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
        $company = GeneralController::getCompany($request);
        $dateReport = $request->get('date-report');

        $roundTripsReport = $this->buildRoundTripsReport($company, $dateReport);

        if ($request->get('export')) $this->export($roundTripsReport);

        return view('reports.vehicles.round-trips.show', compact(['roundTripsReport']));
    }

    /**
     * @param Company|null $company
     * @param $dateReport
     * @return object
     */
    public function buildRoundTripsReport(Company $company = null, $dateReport)
    {
        $vehicles = $company->vehicles;

        $dispatchRegistersByVehicles = DispatchRegister::completed()
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->where('date', $dateReport)
            ->orderBy('id')
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

        PCWExporter::excel([
            'fileName' => __('Round trip report') . " $dateReport",
            'title' => __('Round trip report'),
            'subTitle' => $dateReport,
            'data' => $dataExcel,
            'type' => 'roundTripsVehicleReport'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('reports.route.off-road.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
