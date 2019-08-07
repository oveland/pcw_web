<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 27/11/2018
 * Time: 7:35 PM
 */

namespace App\Services\Reports\Routes;


use App\Http\Controllers\Utils\Geolocation;
use App\Models\Routes\ControlPoint;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use Auth;

class RouteService
{
    /**
     * @var OffRoadService
     */
    private $offRoadService;
    /**
     * @var ConsolidatedService
     */
    public $consolidated;

    /**
     * RouteService constructor.
     * @param OffRoadService $offRoadService
     * @param ConsolidatedService $consolidatedService
     */
    public function __construct(OffRoadService $offRoadService, ConsolidatedService $consolidatedService)
    {
        $this->offRoadService = $offRoadService;
        $this->consolidated = $consolidatedService;
    }


    /**
     * Builds route report for all dispatch register's locations
     *
     * @param DispatchRegister $dispatchRegister
     * @param Location|null $centerOnLocation
     * @return object
     */
    public function buildRouteLocationsReport(DispatchRegister $dispatchRegister, Location $centerOnLocation = null)
    {
        $reports = $dispatchRegister->reports()->with('location')->get();
        $locationsReports = (object)['empty' => $reports->isEmpty(), 'notEmpty' => $reports->isNotEmpty()];

        if ($reports->isNotEmpty()) {
            $vehicle = $dispatchRegister->vehicle;
            $route = $dispatchRegister->route;
            $routeCoordinates = Geolocation::getRouteCoordinates($route->url);
            $controlPoints = $route->controlPoints;
            $controlPointOfReturn = $controlPoints->where('type', ControlPoint::RETURN)->first();

            $routeDistance = $controlPoints->last()->distance_from_dispatch;
            $distanceOfReturn = $controlPointOfReturn ? $controlPointOfReturn->distance_from_dispatch : $routeDistance;

            $reportData = collect([]);
            $lastReport = $reports->first();
            $lastSpeed = 0;
            $totalSpeed = 0;

            foreach ($reports as $report) {
                $location = $report->location;
                if ($report && $location->isValid()) {
                    $offRoad = $location->off_road == 't' ? true : false;

                    $completedPercent = $routeDistance > 0 ? ($report->distancem / $routeDistance) * 100 : 0;
                    if ($completedPercent > 100) $completedPercent = 100;

                    if ($report->controlPoint) {
                        $reportData->push((object)[
                            'locationId' => $location->id,
                            'time' => $report->date->toTimeString(),
                            'timeReport' => $report->timed,
                            'distance' => $report->distancem,
                            'controlPointName' => $report->controlPoint->name,
                            'completedPercent' => number_format($completedPercent, 1, ',', '.'),
                            'value' => $report->status_in_minutes,
                            'latitude' => $location->latitude,
                            'longitude' => $location->longitude,
                            'orientation' => $location->orientation,
                            'trajectoryOfReturn' => $report->distancem >= $distanceOfReturn,
                            'speed' => number_format($location->speed, 1, ',', '.'),
                            'averageSpeed' => ($reportData->count() > 0) ? $totalSpeed / $reportData->count() : 0,
                            'speeding' => $location->speeding,
                            'offRoad' => $offRoad
                        ]);
                    }

                    $lastReport = $report;
                    $lastSpeed = $location->speed;
                    $totalSpeed += $lastSpeed;
                }
            }

            $routePercent = $routeDistance > 0 ? round((($lastReport ? $lastReport->distancem : 0) / $routeDistance) * 100, 1) : 0;

            $center = false;
            if ($centerOnLocation) {
                $center = [
                    'latitude' => $centerOnLocation->latitude,
                    'longitude' => $centerOnLocation->longitude,
                ];
            }

            $offRoadLocations = $reports->pluck('location')->where('off_road',true);
            $offRoadReport = $this->offRoadService->groupByFirstOffRoad($offRoadLocations);

            $locationsReports = (object)[
                'empty' => $reports->isEmpty(),
                'notEmpty' => $reports->isNotEmpty(),
                'date' => $dispatchRegister->date,
                'vehicle' => $vehicle->number,
                'plate' => $vehicle->plate,
                'vehicleSpeed' => round($lastSpeed, 2),
                'route' => $route->name,
                'dispatchRegister' => $dispatchRegister->getAPIFields(),
                'routeDistance' => $routeDistance,
                'routePercent' => $routePercent > 100 ? 100 : $routePercent,
                'controlPoints' => $controlPoints,
                'urlLayerMap' => $route->url,
                'routeCoordinates' => $routeCoordinates->toArray(),
                'reports' => $reportData,
                'offRoadReport' => $offRoadReport,
                'center' => $center
            ];
        }

        return $locationsReports;
    }
}