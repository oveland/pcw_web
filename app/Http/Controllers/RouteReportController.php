<?php

namespace App\Http\Controllers;

use App\Company;
use App\ControlPoint;
use App\DispatchRegister;
use App\Http\Controllers\Utils\Geolocation;
use App\Route;
use Carbon\Carbon;
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
        return view('reports.route', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $route_id = $request->get('route-report');
        $roundTripDispatchRegisters = DispatchRegister::where('date', '=', $request->get('date-report'))
            ->where('route_id', '=', $route_id)
            ->where(function ($query) {
                $query->where('status', '=', 'En camino')->orWhere('status', '=', 'Terminó');
            })
            ->orderBy('round_trip', 'asc')->get()->groupBy('round_trip');

        return view('reports.routeReport', compact('roundTripDispatchRegisters'));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @return \Illuminate\Http\JsonResponse
     */
    public function chart(DispatchRegister $dispatchRegister)
    {
        $dataReport = ['empty' => true];
        $locations = $dispatchRegister->locations()->with('report')->get();
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
                $report = $location->report;
                if ($report && $location->isValid()) {
                    $offRoad = $location->off_road == 't'?true:false;
                    if ($route_coordinates != false) {
                        $offRoad = $this->checkOffRoad($location, $route_coordinates);
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
    public function offRoadReport(DispatchRegister $dispatchRegister, Request $request)
    {
        $locations = $dispatchRegister->locations()->with('report')->get();

        $off_road_report_list = array();

        if ($locations->isNotEmpty()) {
            $route = $dispatchRegister->route;

            $route_coordinates = false;
            if ($dispatchRegister->dateLessThanDateNewOffRoadReport()) {
                $route_coordinates = self::getRouteCoordinates($route->url);
            }

            $reports = array();
            foreach ($locations as $location) {
                $report = $location->report;
                if ($report && $location->isValid()) {
                    $offRoad = $location->off_road == 't'?true:false;
                    if ($route_coordinates != false) {
                        $offRoad = $this->checkOffRoad($location, $route_coordinates);
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
            foreach ($reports as $report) {
                $offRoad = (!$offRoad) ? $report->offRoad : false;
                if ($offRoad) $off_road_report_list[] = $report;
                $offRoad = $report->offRoad;
            }

            if ($request->get('export')) $this->export($dispatchRegister, $off_road_report_list);
        }
        return view('reports.offRoadReport', compact('off_road_report_list', 'dispatchRegister'));
    }

    /**
     * Export report to excel file
     *
     * @param $dispatchRegister
     * @param $off_road_report_list
     */
    public function export($dispatchRegister, $off_road_report_list)
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
    public function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('reports.routeSelect', compact('routes'));
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
    public static function getRouteCoordinates($url)
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
    public function checkOffRoad($location, $route_coordinates)
    {
        $offRoad = true;
        $location_latitude = $location->latitude;
        $location_longitude = $location->longitude;
        //dump($location_latitude.', '.$location_longitude);
        $threshold = 0.005;
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
