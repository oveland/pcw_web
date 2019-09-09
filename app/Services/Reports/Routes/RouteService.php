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
        //$reports = $dispatchRegister->reports()->with('location')->get();
        $locations = $dispatchRegister->locations()->with('report')->get();
        $locationsReports = (object)['empty' => $locations->isEmpty(), 'notEmpty' => $locations->isNotEmpty()];

        if ($locations->isNotEmpty()) {
            $vehicle = $dispatchRegister->vehicle;
            $route = $dispatchRegister->route;
            $routeCoordinates = Geolocation::getRouteCoordinates($route->url);
            $controlPoints = $route->controlPoints;
            $controlPointOfReturn = $controlPoints->where('type', ControlPoint::RETURN)->first();

            $routeDistance = $controlPoints->count() ? $controlPoints->last()->distance_from_dispatch : $route->distance * 1000;
            $distanceOfReturn = $controlPointOfReturn ? $controlPointOfReturn->distance_from_dispatch : $routeDistance;

            $reportData = collect([]);
            $lastReport = $locations->first();
            $lastSpeed = 0;
            $totalSpeed = 0;

            foreach ($locations as $location) {
                $report = $location->report;
                if ($location->isValid()) {
                    $offRoad = $location->off_road == 't' ? true : false;

                    $completedPercent = $routeDistance > 0 ? ($report ? $report->distancem : 0 / $routeDistance) * 100 : 0;
                    if ($completedPercent > 100) $completedPercent = 100;

                    $dispatchRegister = $location->dispatchRegister;

                    $reportData->push((object)[
                        'locationId' => $location->id,
                        'time' => $report ? $report->date->toTimeString() : $location->date->toTimeString(),
                        'timeReport' => $report ? $report->timed : '00:00:00',
                        'distance' => $report ? $report->distancem : 0,
                        'controlPointName' => $report ? $report->controlPoint->name : "---",
                        'completedPercent' => number_format($completedPercent, 1, ',', '.'),
                        'value' => $report ? $report->status_in_minutes : 0,
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'orientation' => $location->orientation,
                        'trajectoryOfReturn' => $report ? $report->distancem >= $distanceOfReturn : false,
                        'speed' => number_format($location->speed, 1, ',', '.'),
                        'averageSpeed' => ($reportData->count() > 0) ? $totalSpeed / $reportData->count() : 0,
                        'speeding' => $location->speeding,
                        'offRoad' => $offRoad,
                        'vehicleStatus' => (object)[
                            'id' => $location->vehicleStatus->id,
                            'status' => $location->vehicleStatus->des_status,
                            'iconClass' => $location->vehicleStatus->icon_class,
                            'mainClass' => $location->vehicleStatus->main_class,
                        ],
                        'dispatchRegister' => $dispatchRegister ? true : null,
                    ]);

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

            $offRoadLocations = $locations->where('off_road',true);
            $offRoadReport = $this->offRoadService->groupByFirstOffRoad($offRoadLocations);

            $locationsReports = (object)[
                'empty' => $locations->isEmpty(),
                'notEmpty' => $locations->isNotEmpty(),
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