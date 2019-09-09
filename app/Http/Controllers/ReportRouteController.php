<?php

namespace App\Http\Controllers;

use App\LastLocation;
use App\Models\Company\Company;
use App\Models\Routes\ControlPoint;
use App\Models\Routes\DispatchRegister;
use App\Http\Controllers\Utils\Geolocation;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\Report;
use App\Models\Routes\Route;
use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use App\Services\Reports\Routes\RouteService;
use App\Traits\CounterByRecorder;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportRouteController extends Controller
{
    /**
     * @var RouteService
     */
    private $routeService;
    /**
     * @var PCWAuthService
     */
    private $authService;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $authService
     * @param RouteService $routeService
     */
    public function __construct(PCWAuthService $authService, RouteService $routeService)
    {

        $this->routeService = $routeService;
        $this->authService = $authService;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $accessProperties = $this->authService->getAccessProperties();
        $companies = $accessProperties->companies;
        $vehicles = $accessProperties->vehicles;
        return view('reports.route.route.index', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $request->get('date-report');
        $typeReport = $request->get('type-report');
        $routeReport = $request->get('route-report');
        $vehicleReport = $request->get('vehicle-report');
        $completedTurns = $request->get('completed-turns');

        if ($routeReport == 'none') return $this->showReportWithOutRoute($request);

        $dispatchRegisters = DispatchRegister::whereCompanyAndDateAndRouteIdAndVehicleId($company, $dateReport, $routeReport, $vehicleReport)
            ->active($completedTurns)
            ->orderBy('departure_time')
            ->get();

        $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id')
            ->sortBy(function ($reports, $vehicleID) {
                return $reports->first()->vehicle->number;
            });

        $reportsByVehicle = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $reportsByVehicle->put($vehicleId, CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegistersByVehicle));
        }

        switch ($typeReport) {
            case 'group-vehicles':
                if ($request->get('export')) $this->exportByVehicle($dispatchRegistersByVehicles, $dateReport);
                $view = 'reports.route.route.routeReportByVehicle';
                break;
            default:
                if ($request->get('export')) $this->exportUngrouped($dispatchRegistersByVehicles, $dateReport);
                $view = 'reports.route.route.routeReportByAll';
                break;

        }

        return view($view, compact(['dispatchRegistersByVehicles', 'reportsByVehicle', 'company', 'dateReport', 'routeReport', 'vehicleReport', 'typeReport', 'completedTurns']));
    }

    public function showReportWithOutRoute(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $request->get('date-report');
        $thresholdKm = $request->get('threshold-km');
        $typeReport = $request->get('type-report');
        $vehicleReport = $request->get('vehicle-report');
        $completedTurns = $request->get('completed-turns');

        if ($dateReport >= Carbon::now()->toDateString()) return view('partials.alerts.onlyPreviousDate');

        if($vehicleReport && $vehicleReport != 'all') $vehiclesId = [$vehicleReport];
        else $vehiclesId = $company->activeVehicles->pluck('id');

        $lastLocations = LastLocation::with('vehicle')
            ->whereBetween('date', ["$dateReport 00:00:00", "$dateReport 23:59:59"])
            ->whereIn('vehicle_id', $vehiclesId)
            ->where('current_mileage', '>', $thresholdKm * 1000)
            ->where('current_mileage', '<', 700 * 1000)
            ->get()->filter(function ($ll) {
                return $ll->gpsIsOK();
            });

        $dispatchRegisters = DispatchRegister::where('date', '=', $dateReport)
            ->whereIn('vehicle_id', $lastLocations->pluck('vehicle_id'))
            ->active($completedTurns)
            ->orderBy('departure_time')
            ->get();

        $lasLocationsOut = $lastLocations->whereNotIn('vehicle_id', $dispatchRegisters->pluck('vehicle_id'));

        return view('reports.route.route.withOutRoute', compact(['lasLocationsOut', 'company', 'dateReport']));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @param $locationId
     * @param Request $request
     * @return string
     */
    public function chartView(DispatchRegister $dispatchRegister, $locationId, Request $request)
    {
        return view('reports.route.route.general', compact(['dispatchRegister', 'locationId']));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chart(DispatchRegister $dispatchRegister, Request $request)
    {
        sleep(1);
        $centerOnLocation = ($request->get('centerOnLocation')) ? Location::find(($request->get('centerOnLocation'))) : null;
        return response()->json($this->routeService->buildRouteLocationsReport($dispatchRegister, $centerOnLocation));
    }

    /**
     * Gets table logs for calculated reports
     *
     * @param DispatchRegister $dispatchRegister
     * @return Factory|View
     */
    public function getReportLog(DispatchRegister $dispatchRegister)
    {
        $reports = Report::where('dispatch_register_id', $dispatchRegister->id)
            ->with('location')
            ->orderBy('date')
            ->get();

        $locationsReports = $this->routeService->buildRouteLocationsReport($dispatchRegister);

        return view('reports.route.route.templates._tableReportLog', compact(['reports', 'locationsReports']));
    }

    /**
     * Export excel by Vehicle option
     *
     * @param $vehiclesDispatchRegisters
     * @param $dateReport
     * @internal param $roundTripDispatchRegisters
     */
    public function exportByVehicle($vehiclesDispatchRegisters, $dateReport)
    {
        Excel::create(__('Dispatch report') . " B " . " $dateReport", function ($excel) use ($vehiclesDispatchRegisters, $dateReport) {
            foreach ($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters) {
                $vehicle = Vehicle::find($vehicleId);
                $vehicleCounter = CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegisters);
                $dataExcel = array();
                $lastArrivalTime = null;

                $totalDeadTime = '00:00:00';

                foreach ($dispatchRegisters as $dispatchRegister) {
                    $historyCounter = $vehicleCounter->report->history[$dispatchRegister->id];
                    $route = $dispatchRegister->route;
                    $driver = $dispatchRegister->driver;

                    $endRecorder = $historyCounter->endRecorder;
                    $startRecorder = $historyCounter->startRecorder;
                    $totalRoundTrip = $historyCounter->passengersByRoundTrip;
                    $totalPassengersByRoute = $historyCounter->totalPassengersByRoute;

                    $deadTime = $lastArrivalTime ? StrTime::subStrTime($dispatchRegister->departure_time, $lastArrivalTime) : '';

                    $dataExcel[] = [
                        __('Route') => $route->name,                                                                    # A CELL
                        __('Round Trip') => $dispatchRegister->round_trip,                                              # B CELL
                        __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                        # C CELL
                        __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # D CELL
                        __('Arrival Time Scheduled') => StrTime::toString($dispatchRegister->arrival_time_scheduled),   # E CELL
                        __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # F CELL
                        __('Arrival Time Difference') => StrTime::toString($dispatchRegister->arrival_time_difference), # G CELL
                        __('Route Time') =>
                            $dispatchRegister->complete() ?
                                StrTime::subStrTime($dispatchRegister->arrival_time, $dispatchRegister->departure_time) :
                                '',                                                                                         # H CELL
                        __('Status') => $dispatchRegister->status,                                                     # I CELL
                        __('Start Rec.') => intval($startRecorder),                                                    # J CELL
                        __('End Rec.') => intval($endRecorder),                                                        # K CELL
                        __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),                          # L CELL
                        __('Pass.') . " " . __('Day') => intval($totalPassengersByRoute),                         # M CELL
                        __('Vehicles without route') => intval($dispatchRegister->available_vehicles),                 # N CELL
                        __('Dead time') => $deadTime,                 # O CELL
                    ];

                    $totalDeadTime = $deadTime ? StrTime::addStrTime($totalDeadTime, $deadTime) : $totalDeadTime;

                    $lastArrivalTime = $dispatchRegister->arrival_time;
                }

                $dataExport = (object)[
                    'fileName' => __('Dispatch report') . " V $dateReport",
                    'title' => __('Dispatch report') . " | $dateReport",
                    'subTitle' => "$vehicle->number | $vehicle->plate" . ". " . __('Total dead time') . ": $totalDeadTime",
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'routeReportByVehicle'
                ];
                /* SHEETS */
                $excel = PCWExporterService::createHeaders($excel, $dataExport);
                $excel = PCWExporterService::createSheet($excel, $dataExport);
            }
        })->export('xlsx');
    }

    /**
     * Export excel by Vehicle option
     *
     * @param $vehiclesDispatchRegisters
     * @param $dateReport
     * @internal param $roundTripDispatchRegisters
     */
    public function exportUngrouped($vehiclesDispatchRegisters, $dateReport)
    {
        Excel::create(__('Dispatch report') . " UG " . " $dateReport", function ($excel) use ($vehiclesDispatchRegisters, $dateReport) {
            $dataExcel = collect([]);

            foreach ($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters) {
                $dispatchRegisters = $dispatchRegisters->sortBy('departure_time');

                $vehicle = Vehicle::find($vehicleId);
                $company = $vehicle->company;
                $vehicleCounter = CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegisters);
                $lastArrivalTime = null;

                $totalDeadTime = '00:00:00';

                foreach ($dispatchRegisters as $dispatchRegister) {
                    $historyCounter = $vehicleCounter->report->history[$dispatchRegister->id];
                    $route = $dispatchRegister->route;
                    $driver = $dispatchRegister->driver;

                    $endRecorder = $historyCounter->endRecorder;
                    $startRecorder = $historyCounter->startRecorder;
                    $totalRoundTrip = $historyCounter->passengersByRoundTrip;
                    $totalPassengersByRoute = $historyCounter->totalPassengersByRoute;

                    $deadTime = $lastArrivalTime ? StrTime::subStrTime($dispatchRegister->departure_time, $lastArrivalTime) : '';

                    $data = collect([
                        __('Vehicle') => $vehicle->number,                                                                    # A CELL
                        __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # E CELL
                        __('Arrival Time Scheduled') => StrTime::toString($dispatchRegister->arrival_time_scheduled),   # F CELL
                        __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # G CELL
                        __('Arrival Time Difference') => StrTime::toString($dispatchRegister->arrival_time_difference), # H CELL
                        __('Route Time') =>
                            $dispatchRegister->complete() ?
                                StrTime::subStrTime($dispatchRegister->arrival_time, $dispatchRegister->departure_time) :
                                '',                                                                                         # I CELL
                        __('Route') => $route->name,                                                                    # A CELL
                        __('Round Trip') => $dispatchRegister->round_trip,                                              # B CELL
                        __('Status') => $dispatchRegister->status,                                                     # J CELL
                        __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                        # D CELL
                    ]);

                    if ($company->hasRecorderCounter()) {
                        $data->put(__('Start Rec.'), intval($startRecorder));
                        $data->put(__('End Rec.'), intval($endRecorder));
                        $data->put(__('Pass.') . " " . __('Round Trip'), intval($totalRoundTrip));
                        $data->put(__('Pass.') . " " . __('Day'), intval($totalPassengersByRoute));
                        $data->put(__('Vehicles without route'), intval($dispatchRegister->available_vehicles));
                        $data->put(__('Dead time'), $deadTime);
                    }

                    $dataExcel->push($data->toArray());

                    $totalDeadTime = $deadTime ? StrTime::addStrTime($totalDeadTime, $deadTime) : $totalDeadTime;

                    $lastArrivalTime = $dispatchRegister->arrival_time;
                }

                $data = collect([
                    __('Vehicle') => '----------',
                    __('Departure time') => '----------',
                    __('Arrival Time Scheduled') => '----------',
                    __('Arrival Time') => '----------',
                    __('Arrival Time Difference') => '----------',
                    __('Route Time') => '----------',
                    __('Route') => strtoupper(__('Total round trips')),
                    __('Dead time') => number_format($dispatchRegisters->count() / 2, '1', '.', '')
                ]);

                $dataExcel->push($data->toArray());
            }

            $dataExport = (object)[
                'fileName' => __('Dispatch report') . " $dateReport",
                'title' => __('Dispatch report') . " | $dateReport",
                'subTitle' => __('Total vehicles') . ": " . $vehiclesDispatchRegisters->count(),
                'sheetTitle' => __('Dispatch report'),
                'data' => $dataExcel->toArray(),
                'type' => 'routeReportUngrouped'
            ];
            /* SHEETS */
            $excel = PCWExporterService::createHeaders($excel, $dataExport);
            $excel = PCWExporterService::createSheet($excel, $dataExport);
        })->export('xlsx');
    }

    /**
     * @param Request $request
     * @return Factory|View|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                return (new GeneralController())->loadSelectRoutes($request);

                /*$company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('partials.selects.routes', compact('routes'));*/
                break;
            case 'executeDAR':
                ini_set('MAX_EXECUTION_TIME', 0);
                set_time_limit(0);
                $dispatchRegisterId = $request->get('dispatchRegisterId');

                $client = new Client();
                $response = $client->request('GET', config('gps.server.url') . "/autoDispatcher/processDispatchRegister/$dispatchRegisterId?sync=true", ['timeout' => 0]);

                return $response->getBody();
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
