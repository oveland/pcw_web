<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\ControlPoint;
use App\Models\Routes\DispatchRegister;
use App\Http\Controllers\Utils\Geolocation;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\Report;
use App\Models\Routes\Route;
use App\Models\Vehicles\Location;
use App\Services\PCWExporterService;
use App\Services\Reports\Routes\RouteService;
use App\Traits\CounterByRecorder;
use App\Models\Vehicles\Vehicle;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportRouteController extends Controller
{
    /**
     * @var RouteService
     */
    private $routeService;

    /**
     * ReportRouteController constructor.
     * @param RouteService $routeService
     */
    public function __construct(RouteService $routeService)
    {

        $this->routeService = $routeService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->get();
        }
        return view('reports.route.route.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $companyReport = $request->get('company-report');
        $routeReport = $request->get('route-report');
        $dateReport = $request->get('date-report');
        $typeReport = $request->get('type-report');

        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;
        $route = $routeReport == "all" ? $routeReport : Route::find($routeReport);
        if ($routeReport != "all" && (!$route || !$route->belongsToCompany($company))) abort(404);

        $dispatchRegisters = DispatchRegister::where('date', '=', $dateReport);
        if ($routeReport != "all") $dispatchRegisters = $dispatchRegisters->where('route_id', '=', $route->id);
        else $dispatchRegisters = $dispatchRegisters->whereIn('route_id', $company->routes->pluck('id'));
        $dispatchRegisters = $dispatchRegisters
            ->active()
            ->orderBy('departure_time')
            ->get();

        switch ($typeReport) {
            case 'group-vehicles':
                $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id')
                    ->sortBy(function ($reports, $vehicleID) {
                        return $reports->first()->vehicle->number;
                    });

                $reportsByVehicle = collect([]);
                foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
                    $reportsByVehicle->put($vehicleId, CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegistersByVehicle));
                }

                if ($request->get('export')) $this->exportByVehicle($dispatchRegistersByVehicles, $dateReport);

                return view('reports.route.route.routeReportByVehicle', compact(['dispatchRegistersByVehicles', 'reportsByVehicle', 'company', 'route', 'dateReport', 'routeReport', 'typeReport']));
                break;
            default:
                $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id');
                $reportsByVehicle = collect([]);
                foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
                    $reportsByVehicle->put($vehicleId, CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegistersByVehicle));
                }

                return view('reports.route.route.routeReportByAll', compact(['dispatchRegisters', 'reportsByVehicle', 'company', 'route', 'dateReport', 'routeReport', 'typeReport']));
                break;

        }
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
        $centerOnLocation = ($request->get('centerOnLocation')) ? Location::find(($request->get('centerOnLocation'))) : null;
        return response()->json($this->routeService->buildRouteLocationsReport($dispatchRegister, $centerOnLocation));
    }

    /**
     * Gets table logs for calculated reports
     *
     * @param DispatchRegister $dispatchRegister
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
                        __('Turn') => $dispatchRegister->turn,                                                          # C CELL
                        __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                        # D CELL
                        __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # E CELL
                        __('Arrival Time Scheduled') => StrTime::toString($dispatchRegister->arrival_time_scheduled),   # F CELL
                        __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # G CELL
                        __('Arrival Time Difference') => StrTime::toString($dispatchRegister->arrival_time_difference), # H CELL
                        __('Route Time') =>
                            $dispatchRegister->complete() ?
                                StrTime::subStrTime($dispatchRegister->arrival_time, $dispatchRegister->departure_time) :
                                '',                                                                                         # I CELL
                        __('Status') => $dispatchRegister->status,                                                     # J CELL
                        __('Start Rec.') => intval($startRecorder),                                                    # K CELL
                        __('End Rec.') => intval($endRecorder),                                                        # L CELL
                        __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),                          # M CELL
                        __('Pass.') . " " . __('Day') => intval($totalPassengersByRoute),                         # N CELL
                        __('Vehicles without route') => intval($dispatchRegister->available_vehicles),                 # O CELL
                        __('Dead time') => $deadTime,                 # P CELL
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
     * @param DispatchRegister $dispatchRegister
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public
    function offRoadReport(DispatchRegister $dispatchRegister, Request $request)
    {
        $reports = $dispatchRegister->reports()->with('location')->get();
        $off_road_report_list = array();
        if ($reports->isNotEmpty()) {
            $reportData = array();
            foreach ($reports as $report) {
                $location = $report->location;
                if ($report && $location->isValid()) {
                    $offRoad = $location->off_road == 't' ? true : false;
                    $reportData[] = (object)[
                        'date' => $report->date,
                        'time' => $report->timed,
                        'distance' => $report->distancem,
                        'value' => $report->status_in_minutes,
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'offRoad' => $offRoad
                    ];
                }
            }

            $offRoad = false;
            $export = $request->get('export');

            foreach ($reportData as $report) {
                if ((!$offRoad) ? $report->offRoad : false) $off_road_report_list[] = $report;
                $offRoad = $report->offRoad;
            }

            if ($export) $this->exportOffRoads($dispatchRegister, $off_road_report_list);
        }

        return view('reports.route.route.offRoadReport', compact('off_road_report_list', 'dispatchRegister'));
    }

    /**
     * Export report to excel file
     *
     * @param $dispatchRegister
     * @param $off_road_report_list
     */
    public
    function exportOffRoads($dispatchRegister, $off_road_report_list)
    {
        $route = $dispatchRegister->route;
        $driver = $dispatchRegister->driver;
        $company = $route->company;
        $dateReport = $dispatchRegister->date;
        $data = [];
        $number = 1;
        foreach ($off_road_report_list as $off_road_report) {
            if ($off_road_report->latitude != 0 && $off_road_report->longitude != 0) {
                $data[] = [
                    'N°' => $number++,
                    __('Date') => $off_road_report->date,
                    __('Status') => $off_road_report->time,
                    __('Latitude') => $off_road_report->latitude,
                    __('Longitude') => $off_road_report->longitude,
                    __('Address') => Geolocation::getAddressFromCoordinates($off_road_report->latitude, $off_road_report->longitude),
                ];
            }
        }

        $dataExport = (object)[
            'fileName' => __('Off_Road_Report_') . str_replace(' ', '_', $company->name) . '.' . str_replace('-', '', $dateReport),
            'header' => [strtoupper(__('Off road report')) . ' ' . $company->name . '. ' . __('Vehicle') . ' ' . $dispatchRegister->vehicle->number . ' ➜ ' . $dispatchRegister->vehicle->plate],
            'infoRoute' => [
                $route->name . ': ' . __('Round Trip') . ' ' . ($dispatchRegister->round_trip == 0 ? '0' : $dispatchRegister->round_trip) . ', ' . __('Turn') . ' ' . $dispatchRegister->turn . '. ' . __('Driver') . ': ' . ($driver ? $driver->fullName() : __('Not assigned')),
            ],
            'data' => $data,
        ];

        Excel::create($dataExport->fileName, function ($excel) use ($dataExport) {
            /* INFO DOCUMENT */
            $excel->setTitle(__('Off road report'));
            $excel->setCreator(__('PCW Ditech Integradores Tecnológicos'))->setCompany(__('PCW Ditech Integradores Tecnológicos'));
            $excel->setDescription(__('Report vehicle off road'));

            /* FIRST SHEET */
            $excel->sheet(__('Off road report'), function ($sheet) use ($dataExport) {
                $totalRows = count($dataExport->data) + 3;

                $sheet->fromArray($dataExport->data);
                $sheet->prependRow($dataExport->infoRoute);
                $sheet->prependRow($dataExport->header);

                /* GENEREAL STYLE */
                $sheet->setOrientation('landscape');
                $sheet->setFontFamily('Segoe UI Light');
                $sheet->setBorder('A1:F' . $totalRows, 'thin');
                $sheet->cells('A1:F' . $totalRows, function ($cells) {
                    $cells->setFontFamily('Segoe UI Light');
                });

                /* SORTABLE COLUMN HEADERS */
                $sheet->setAutoFilter('A3:F' . ($totalRows));

                /*  MAIN HEADER */
                $sheet->setHeight(1, 50);
                $sheet->mergeCells('A1:F1');
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0e6d62');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family' => 'Segoe UI Light',
                        'size' => '14',
                        'bold' => true
                    ));
                });

                /* INFO HEADER */
                $sheet->setHeight(2, 25);
                $sheet->mergeCells('A2:F2');
                $sheet->cells('A2:F2', function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0d4841');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family' => 'Segoe UI Light',
                        'size' => '12',
                        'bold' => true
                    ));
                });

                /* HEADER COLUMNS */
                $sheet->setHeight(3, 40);
                $sheet->cells('A3:F3', function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0d4841');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family' => 'Segoe UI Light',
                        'size' => '12',
                        'bold' => true
                    ));
                });
            });
        })->export('xlsx');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('partials.selects.routes', compact('routes'));
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
