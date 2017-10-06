<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Http\Controllers\Utils\Geolocation;
use App\Route;
use App\Services\PCWExporter;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class RouteReportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::where('active', '=', true)->orderBy('short_name', 'asc')->get();
        }
        return view('reports.route.route.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $route = Route::find($request->get('route-report'));
        $dateReport = $request->get('date-report');
        $typeReport = $request->get('type-report');

        $dispatchRegisters = DispatchRegister::where('date', '=', $dateReport)
            ->where('route_id', '=', $route->id)
            ->where(function ($query) {
                $query->where('status', '=', 'En camino')->orWhere('status', '=', 'Terminó');
            })
            ->with('recorderCounter')
            ->orderBy('round_trip')
            ->orderBy('turn')
            ->get();

        switch ($typeReport) {
            case 'round_trip':
                $roundTripDispatchRegisters = $dispatchRegisters->groupBy('round_trip');

                if ($request->get('export')) $this->exportByRoundTrip($roundTripDispatchRegisters, $route, $dateReport);

                return view('reports.route.route.routeReportByRoundTrip', compact(['roundTripDispatchRegisters', 'route', 'dateReport']));
                break;
            case 'vehicle':
                $vehiclesDispatchRegisters = $dispatchRegisters->groupBy('vehicle_id');

                if ($request->get('export')) $this->exportByVehicle($vehiclesDispatchRegisters, $route, $dateReport);

                return view('reports.route.route.routeReportByVehicle', compact(['vehiclesDispatchRegisters', 'route', 'dateReport']));
                break;
        }
    }

    /**
     * Export excel by Round Trip option
     *
     * @param $roundTripDispatchRegisters
     * @param $route
     * @param $dateReport
     */
    public function exportByRoundTrip($roundTripDispatchRegisters, $route, $dateReport)
    {
        //dd($roundTripDispatchRegisters);
        Excel::create(__('Dispatch report') . " A " . " $dateReport", function ($excel) use ($roundTripDispatchRegisters, $dateReport, $route) {
            foreach ($roundTripDispatchRegisters as $roundTrip => $dispatchRegisters) {
                $dataExcel = array();
                foreach ($dispatchRegisters as $dispatchRegister) {
                    $vehicle = $dispatchRegister->vehicle;
                    $dataExcel[] = [
                        __('N°') => count($dataExcel) + 1,                                                                      # A CELL
                        __('Turn') => $dispatchRegister->turn,                                                                  # B CELL
                        __('Vehicle') => intval($vehicle->number),                                                              # C CELL
                        __('Plate') => $vehicle->plate,                                                                         # D CELL
                        __('Departure time') => $dispatchRegister->departure_time,                                              # E CELL
                        __('Arrival Time Scheduled') => $dispatchRegister->arrival_time_scheduled,                              # F CELL
                        __('Arrival Time') => $dispatchRegister->arrival_time,                                                  # G CELL
                        __('Arrival Time Difference') => $dispatchRegister->arrival_time_difference,                            # H CELL
                        __('Status') => $dispatchRegister->status,                                                              # I CELL
                        __('Passengers') . ' | ' . __('Day') => intval($dispatchRegister->recorderCounter->passengers),    # J CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => __('Dispatch report') . " $dateReport",
                    'title' => __('Dispatch report') . " | $route->name: $dateReport",
                    'subTitle' => __('Round Trip') . " $roundTrip",
                    'data' => $dataExcel
                ];

                /* SHEETS */
                $excel = PCWExporter::createHeaders($excel, $dataExport);
                $excel = PCWExporter::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
    }

    /**
     * Export excel by Vehicle option
     *
     * @param $roundTripDispatchRegisters
     * @param $route
     * @param $dateReport
     */
    public function exportByVehicle($vehiclesDispatchRegisters, $route, $dateReport)
    {
        //dd($roundTripDispatchRegisters);
        Excel::create(__('Dispatch report') . " B " . " $dateReport", function ($excel) use ($vehiclesDispatchRegisters, $dateReport, $route) {
            foreach ($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters) {
                $vehicle = Vehicle::find($vehicleId);
                $dataExcel = array();
                foreach ($dispatchRegisters as $dispatchRegister) {
                    $startRecorder = $dispatchRegister->recorderCounter->getStartRecorder();
                    $currentRecorder = $dispatchRegister->recorderCounter->end_recorder;
                    $totalDay = $dispatchRegister->recorderCounter->passengers;
                    $totalRoundTrip = isset($lastRecorder) ? $currentRecorder - $lastRecorder : $totalDay;
                    $lastRecorder = $currentRecorder;
                    $dataExcel[] = [
                        __('Round Trip') => $dispatchRegister->round_trip,                                  # A CELL
                        __('Turn') => $dispatchRegister->turn,                                              # B CELL
                        __('Departure time') => $dispatchRegister->departure_time,                          # C CELL
                        __('Arrival Time Scheduled') => $dispatchRegister->arrival_time_scheduled,          # D CELL
                        __('Arrival Time') => $dispatchRegister->arrival_time,                              # E CELL
                        __('Arrival Time Difference') => $dispatchRegister->arrival_time_difference,        # F CELL
                        __('Status') => $dispatchRegister->status,                                          # G CELL
                        __('Start Rec.') => intval($startRecorder),                                         # H CELL
                        __('End Rec.') => intval($dispatchRegister->end_recorder),                          # I CELL
                        __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),          # J CELL
                        __('Pass.') . " " . __('Day') => intval($totalDay),                       # K CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => __('Dispatch report') . " $dateReport",
                    'title' => __('Dispatch report') . " $dateReport",
                    'subTitle' => "$vehicle->number | $vehicle->plate",
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'routeReportByVehicle'
                ];
                //foreach ()
                /* SHEETS */
                $excel = PCWExporter::createHeaders($excel, $dataExport);
                $excel = PCWExporter::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @return \Illuminate\Http\JsonResponse
     */
    public
    function chart(DispatchRegister $dispatchRegister)
    {
        $dataReport = ['empty' => true];
        //$locations = $dispatchRegister->locations()->with('report')->get();
        $locations = $dispatchRegister->locationReports()->get();
        $offRoadLocation = true;
        if ($locations->isNotEmpty()) {
            $vehicle = $dispatchRegister->vehicle;

            $route = $dispatchRegister->route;
            $routeDistance = $route->distance * 1000;
            $controlPoints = $route->controlPoints;

            $route_coordinates = false;
            if ($dispatchRegister->dateLessThanDateNewOffRoadReport()) {
                $route_coordinates = self::getRouteCoordinates($route->url);
                $offRoadLocation = false;
            }

            $reports = array();
            $lastReport = null;
            $lastLocation = null;

            foreach ($locations as $location) {
                //$report = $location->report;
                $report = $location;
                if ($report && $location->isValid()) {
                    $offRoad = $location->off_road == 't' ? true : false;
                    if ($route_coordinates != false) {
                        $offRoad = self::checkOffRoad($location, $route_coordinates);
                    }

                    $reports[] = (object)[
                        'date' => $report->date,
                        'time' => $report->timed,
                        'distance' => $report->distancem,
                        'value' => $report->status_in_minutes,
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'offRoad' => $offRoad
                    ];

                    $lastReport = $report ? $report : $lastReport;
                    $lastLocation = $location;
                }
            }

            $dataReport = (object)[
                'vehicle' => $vehicle->number,
                'plate' => $vehicle->plate,
                'vehicleSpeed' => round($lastLocation ? $lastLocation->speed : 0, 2),
                'route' => $route->name,
                'routeDistance' => $routeDistance,
                'routePercent' => round((($lastReport ? $lastReport->distancem : 0) / $routeDistance) * 100, 1),
                'controlPoints' => $controlPoints,
                'urlLayerMap' => $route->url,
                'reports' => $reports,
                'offRoadFromLocation' => $offRoadLocation
            ];
        }

        return response()->json($dataReport);
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public
    function offRoadReport(DispatchRegister $dispatchRegister, Request $request)
    {
        //$locations = $dispatchRegister->locations()->with('report')->get();
        $locations = $dispatchRegister->locationReports()->get();

        $off_road_report_list = array();

        if ($locations->isNotEmpty()) {
            $route = $dispatchRegister->route;

            $route_coordinates = false;
            if ($dispatchRegister->dateLessThanDateNewOffRoadReport()) {
                $route_coordinates = self::getRouteCoordinates($route->url);
            }

            $reports = array();
            foreach ($locations as $location) {
                //$report = $location->report;
                $report = $location;
                if ($report && $location->isValid()) {
                    $offRoad = $location->off_road == 't' ? true : false;
                    if ($route_coordinates != false) {
                        $offRoad = self::checkOffRoad($location, $route_coordinates);
                    }
                    $reports[] = (object)[
                        'date' => $report->date,
                        'time' => $report->timed,
                        'distance' => $report->distancem,
                        'value' => $report->status_in_minutes,
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'offRoad' => $offRoad//$this->checkOffRoad($location, $route_coordinates)
                    ];
                }
            }

            $offRoad = false;
            $export = $request->get('export');
            foreach ($reports as $report) {
                if ((!$offRoad || $export) ? $report->offRoad : false) $off_road_report_list[] = $report;
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
            'infoRoute' => [$route->name . ': ' . __('Round Trip') . ' ' . ($dispatchRegister->round_trip == 0 ? '0' : $dispatchRegister->round_trip) . ', ' . __('Turn') . ' ' . $dispatchRegister->turn],
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
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('reports.route.route.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }

    /**
     * Get route coordinates from google kmz file
     *
     * @param $url
     * @return array
     */
    public
    static function getRouteCoordinates($url)
    {
        $milliseconds = round(microtime(true) * 1000);
        $dir_name = "ziptmp$milliseconds";
        $file = 'doc.kml';

        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $temp = tempnam(sys_get_temp_dir(), $ext);
        copy($url, $temp);

        $zip = new ZipArchive();
        if ($zip->open($temp, ZIPARCHIVE::CREATE) === TRUE) {
            $zip->extractTo($dir_name);
            $zip->close();
        }

        $data = file_get_contents($dir_name . '/' . $file);

        unlink($temp);
        array_map('unlink', glob("$dir_name/*.*"));
        chmod($dir_name, 0777);
        rmdir($dir_name);

        $dataXML = simplexml_load_string($data);
        $documents = $dataXML->Document->Folder;
        $documents = $documents->Placemark ? $documents : $dataXML->Document;

        /* Extract coordinates for xml file */
        $route_coordinates = array();
        foreach ($documents as $document) {
            foreach ($document->Placemark as $placemark) {
                $route_coordinates_xml = explode(' ', trim($placemark->LineString->coordinates));
                foreach ($route_coordinates_xml as $index => $route_coordinate) {
                    $array_coordinates = collect(explode(',', trim($route_coordinate)));

                    if ($array_coordinates->count() > 2) {
                        list($longitude, $latitude, $angle) = explode(',', trim($route_coordinate));
                        $latitude_route = doubleval($latitude);
                        $longitude_route = doubleval($longitude);
                        $route_coordinates[] = [
                            'latitude' => $latitude_route,
                            'longitude' => $longitude_route
                        ];
                    }
                }
            }
        }

        return $route_coordinates;
    }

    /**
     * Check if location is off road from kml route coordinates
     *
     * @param $location
     * @param $route_coordinates
     * @return bool
     */
    public
    static function checkOffRoad($location, $route_coordinates)
    {
        $offRoad = true;
        $location_latitude = $location->latitude;
        $location_longitude = $location->longitude;
        //dump($location_latitude.', '.$location_longitude);
        $threshold = config('road.route_sampling_area');
        $threshold_location = [
            'la_up' => $location_latitude + $threshold,
            'la_down' => $location_latitude - $threshold,
            'lo_up' => $location_longitude + $threshold,
            'lo_down' => $location_longitude - $threshold
        ];
        /*dump($threshold_location['la_up'].', '.$threshold_location['lo_up']);
        dump($threshold_location['la_up'].', '.$threshold_location['lo_down']);
        dump($threshold_location['la_down'].', '.$threshold_location['lo_up']);
        dd($threshold_location['la_down'].', '.$threshold_location['lo_down']);*/


        $route_coordinates = collect($route_coordinates);
        $route_coordinates = $route_coordinates->filter(function ($value, $key) use ($threshold_location) {
            return
                $value['latitude'] > $threshold_location['la_down'] && $value['latitude'] < $threshold_location['la_up'] &&
                $value['longitude'] > $threshold_location['lo_down'] && $value['longitude'] < $threshold_location['lo_up'];
        })->values()->toArray();

        foreach ($route_coordinates as $index => $route_coordinate) {
            $route_latitude = $route_coordinate['latitude'];
            $route_longitude = $route_coordinate['longitude'];

            $radius_distance = Geolocation::getDistance($location_latitude, $location_longitude, $route_latitude, $route_longitude);

            if ($radius_distance <= config('road.route_distance_threshold')) {
                $offRoad = false;
                break;
            } else if ($radius_distance < config('road.route_sampling_radius') && $index > 0) {
                $prev_route_latitude = $route_coordinates[$index - 1]['latitude'];
                $prev_route_longitude = $route_coordinates[$index - 1]['longitude'];
                $a = (double)$radius_distance;
                $b = (double)Geolocation::getDistance($location_latitude, $location_longitude, $prev_route_latitude, $prev_route_longitude);
                $c = (double)Geolocation::getDistance($route_latitude, $route_longitude, $prev_route_latitude, $prev_route_longitude);
                $angle = Geolocation::getAngleC($a, $b, $c);
                $thresholdAngle = Geolocation::getThresholdAngleC(config('road.route_distance_threshold'), $a, $b);

                if ($angle >= $thresholdAngle) {
                    $offRoad = false;
                    break;
                }
            }
        }
        return $offRoad;
    }
}
