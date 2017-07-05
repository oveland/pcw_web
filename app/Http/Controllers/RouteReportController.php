<?php

namespace App\Http\Controllers;

use App\Company;
use App\ControlPoint;
use App\DispatchRegister;
use App\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $locations = collect($dispatchRegister->locations);
        $report_list = collect($dispatchRegister->reports);

        if ($locations->isNotEmpty()) {
            $route = $dispatchRegister->route;
            $controlPoints = $route->controlPoints;
            $vehicle = $dispatchRegister->vehicle;

            $route_coordinates = $this->getRouteCoordinates($route->url);
            $routeDistance = $route->distance * 1000;

            $reports = array();
            foreach ($locations as $location) {
                $report = $report_list->where('location_id','=',$location->id)->first();
                //$report = $location->report;
                /* The first location haven´t a report */
                if ($report) {
                    $reports[] = (object)[
                        'date' => $report->date,
                        'time' => $report->timed,
                        'distance' => $report->distancem,
                        'value' => $report->status_in_minutes,
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'offRoad' => $this->checkOffRoad($location, $route_coordinates)
                    ];
                }
            }

            $dataReport = collect([
                'vehicle' => $vehicle->number,
                'plate' => $vehicle->plate,
                'vehicleSpeed' => round($locations->last()->report->speed, 2),
                'route' => $route->name,
                'routeDistance' => $routeDistance,
                'routePercent' => round(($locations->last()->report->distancem / $routeDistance) * 100, 1),
                'controlPoints' => $controlPoints,
                'urlLayerMap' => $route->url,
                'reports' => $reports
            ]);
        }

        return response()->json($dataReport);
    }

    public function offRoadReport()
    {

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                if (Auth::user()->isAdmin()) {
                    $company = $request->get('company');
                } else {
                    $company = Auth::user()->company->id;
                }
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
    public function getRouteCoordinates($url)
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
        $documents = $documents ? $documents : $dataXML->Document;

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
        $location_longitude = $location->longitude;
        $location_latitude = $location->latitude;

        foreach ($route_coordinates as $index => $route_coordinate) {
            $route_latitude = $route_coordinate['latitude'];
            $route_longitude = $route_coordinate['longitude'];

            $radius_distance = $this->getDistance($location_latitude, $location_longitude, $route_latitude, $route_longitude);

            if ($radius_distance <= config('road.route_distance_threshold')) {
                $offRoad = false;
                break;
            } else if ($radius_distance < config('road.route_sampling_radius') && $index > 0) {
                $prev_route_latitude = $route_coordinates[$index - 1]['latitude'];
                $prev_route_longitude = $route_coordinates[$index - 1]['longitude'];
                $a = (double)$radius_distance;
                $b = (double)$this->getDistance($location_latitude, $location_longitude, $prev_route_latitude, $prev_route_longitude);
                $c = (double)$this->getDistance($route_latitude, $route_longitude, $prev_route_latitude, $prev_route_longitude);
                $angle = $this->getAngleC($a, $b, $c);
                $thresholdAngle = $this->getThresholdAngleC(config('road.route_distance_threshold'), $a, $b);

                if ($angle >= $thresholdAngle) {
                    $offRoad = false;
                    break;
                }
            }
        }
        return $offRoad;
    }

    /**
     * Get distance in meters from two coordinates in decimal
     *
     * @param $latitude1
     * @param $longitude1
     * @param $latitude2
     * @param $longitude2
     * @return int
     */
    public function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $earth_radius = 6371;

        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d * 1000;
    }

    /**
     * Get angle of C between three distances (a,b,c) through Law of Cosines
     *
     * @param $a
     * @param $b
     * @param $c
     * @return float
     */
    public function getAngleC($a, $b, $c)
    {
        $argument = (pow($a, 2) + pow($b, 2) - pow($c, 2)) / (2 * $a * $b);
        if (abs($argument) > 1) return 180;/* Assumes on road */
        $angle_radians = acos($argument);
        return rad2deg($angle_radians);
    }

    /**
     * Get the angle C for threshold distance from route road
     *
     * @param $threshold_distance
     * @param $a
     * @param $b
     * @return float
     */
    public function getThresholdAngleC($threshold_distance, $a, $b)
    {
        return rad2deg(acos($threshold_distance / $a) + acos($threshold_distance / $b));
    }
}
