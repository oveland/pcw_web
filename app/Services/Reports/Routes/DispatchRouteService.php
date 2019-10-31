<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 9/12/2018
 * Time: 12:09 AM
 */

namespace App\Services\Reports\Routes;

use App\Http\Controllers\Utils\StrTime;
use App\LastLocation;
use App\Models\Company\Company;
use App\Models\Routes\ControlPoint;
use App\Models\Routes\DispatchRegister;
use App\Http\Controllers\Utils\Geolocation;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DispatchRouteService
{
    /**
     * @var OffRoadService
     */
    public $offRoad;
    /**
     * @var SpeedingService
     */
    public $speeding;
    /**
     * @var ControlPointService
     */
    public $controlPoints;

    /**
     * ReportsService constructor.
     * @param OffRoadService $offRoadService
     * @param SpeedingService $speedingService
     * @param ControlPointService $controlPointService
     */
    public function __construct(OffRoadService $offRoadService, SpeedingService $speedingService, ControlPointService $controlPointService)
    {
        $this->offRoad = $offRoadService;
        $this->speeding = $speedingService;
        $this->controlPoints = $controlPointService;
    }

    /**
     * Get all dispatch registers
     *
     * @param $company
     * @param $dateReport
     * @param $routeReport
     * @param $vehicleReport
     * @param $completedTurns
     * @return DispatchRegister[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all($company, $dateReport, $routeReport = 'all', $vehicleReport = 'all', $completedTurns = true)
    {
        return DispatchRegister::whereCompanyAndDateAndRouteIdAndVehicleId($company, $dateReport, $routeReport, $vehicleReport)
            ->active($completedTurns)
            ->orderBy('departure_time')
            ->get();
    }

    /**
     * @param $company
     * @param $dateReport
     * @param $routeReport
     * @param $vehicleReport
     * @param $completedTurns
     * @return DispatchRegister[]|Builder[]|Collection
     */
    public function allByVehicles($company, $dateReport, $routeReport = 'all', $vehicleReport = 'all', $completedTurns = true)
    {
        $dispatchRegisters = $this->all($company, $dateReport, $routeReport, $vehicleReport, $completedTurns);

        return $dispatchRegisters->groupBy('vehicle_id')
            ->sortBy(function ($reports, $vehicleID) {
                return $reports->first()->vehicle->number;
            });
    }

    /**
     * Builds route report for all dispatch register's locations
     *
     * @param DispatchRegister $dispatchRegister
     * @param Location|null $centerOnLocation
     * @return object
     */
    public function locationsReports(DispatchRegister $dispatchRegister, Location $centerOnLocation = null)
    {
        //$reports = $dispatchRegister->reports()->with('location')->get();
        $locations = $dispatchRegister->locations()->with('report')->get();
        $locationsReports = (object)['empty' => $locations->isEmpty(), 'notEmpty' => $locations->isNotEmpty()];

        if ($locations->isNotEmpty()) {
            $locations = $locations->sortBy('date');
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

                    $completedPercent = $routeDistance > 0 ? (($report ? $report->distancem : 0) / $routeDistance) * 100 : 0;
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

            $offRoadLocations = $locations->where('off_road', true);
            $offRoadReport = $this->offRoad->groupByFirstOffRoad($offRoadLocations);

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

    /**
     * Gets consolidated data from dispatch registers
     *
     * @param Collection $dispatchRegistersByVehicle
     * @param Company|null $company
     * @return Collection
     */
    public function getConsolidatedDataDispatches($dispatchRegistersByVehicle, Company $company = null)
    {
        $reportVehicleByRoute = collect([]);
        if ($dispatchRegistersByVehicle->isNotEmpty()) {
            $company = $company ? $company : $dispatchRegistersByVehicle->first()->route->company;

            foreach ($dispatchRegistersByVehicle as $dispatchRegister) {
                $vehicle = $dispatchRegister->vehicle;
                $locations = $dispatchRegister->locations;

                if ($locations->isNotEmpty()) {
                    $speedingLocations = $locations->where('speeding', true);

                    $offRoadReport = $this->offRoad->groupByFirstOffRoad($locations);
                    $speedingReport = $this->speeding::groupByFirstSpeedingEvent($speedingLocations);
                    $controlPointReport = $this->controlPoints->reportWithDelay($dispatchRegister);

                    $totalOffRoads = $offRoadReport->count();
                    $totalSpeeding = $speedingReport->count();

                    $locationWithMaxSpeed = $locations->sortByDesc('speed')->first();

                    $controlPointReportTotal = $controlPointReport->count();
                    $hasEvent = ($totalOffRoads > 0 || $totalSpeeding > 0 || ($company->hasControlPointEventsActive() && ($controlPointReportTotal > 0)));

                    $reportVehicleByRoute->put($dispatchRegister->id, (object)[
                        'vehicleId' => $vehicle->id,
                        'vehicle' => $vehicle,
                        'dispatchRegister' => $dispatchRegister,

                        'offRoadReport' => $offRoadReport,
                        'offRoadPercent' => $dispatchRegister->totalOffRoad,
                        'totalOffRoads' => $totalOffRoads,

                        'speedingReport' => $speedingReport,
                        'maxSpeed' => $locationWithMaxSpeed->speed ?? 0,
                        'maxSpeedTime' => $locationWithMaxSpeed->date->toTimeString() ?? null,
                        'totalSpeeding' => $totalSpeeding,

                        'controlPointReport' => $controlPointReport,
                        'controlPointReportTotal' => $controlPointReportTotal,

                        'hasEvent' => $hasEvent
                    ]);
                }
            }
        }

        return $reportVehicleByRoute;
    }

    /**
     * @param Company $company
     * @param $dateReport
     * @param string $routeReport
     * @param string $vehicleReport
     * @param bool $completedTurns
     * @return Collection
     */
    function buildDailyEventsReport(Company $company, $dateReport, $routeReport = 'all', $vehicleReport = 'all', $completedTurns = true)
    {
        $allDispatchRegisters = $this->all($company, $dateReport, $routeReport, $vehicleReport, $completedTurns);

        $eventsReports = collect([]);
        $routes = $company->activeRoutes
            ->where('id', '<>', 183); // TODO: Let route 183 when all parameters are configured

        foreach ($routes as $route) {
            $dispatchRegisters = $allDispatchRegisters->where('route_id', $route->id)->sortBy('departure_time');
            $reportVehicleByRoute = $this->getConsolidatedDataDispatches($dispatchRegisters, $company)->where('hasEvent', true);

            $eventsReports->put($route->id, (object)[
                'route' => $route,
                'date' => $dateReport,
                'reportVehicleByRoute' => $reportVehicleByRoute,
                'totalReports' => $reportVehicleByRoute->count()
            ]);
        }

        return $eventsReports;
    }

    /**
     * @param DispatchRegister[] | Collection $allDispatchRegistersByVehicle
     * @param Company $company
     * @return int $totalRoundTrips
     */
    private function getTotalRoundTrips($allDispatchRegistersByVehicle, Company $company = null)
    {
        if ($allDispatchRegistersByVehicle->isEmpty()) return 0;

        $company = $company ? $company : $allDispatchRegistersByVehicle->first()->route->company;

        $totalRoundTrips = 0;

        $dispatchRegistersByRoute = collect($allDispatchRegistersByVehicle)->groupBy('route_id');

        foreach ($dispatchRegistersByRoute as $routeId => $dispatchRegisters) {
            $totalRoundTrips += (int)$dispatchRegisters->max('round_trip');
        }

        if ($company->isIntermunicipal()) $totalRoundTrips = (double)$totalRoundTrips / $dispatchRegistersByRoute->count();

        return $totalRoundTrips;
    }

    /**
     * @param Collection $dispatchRegistersByVehicle
     * @return Collection
     */
    public function getRouteTurns($dispatchRegistersByVehicle)
    {
        $dispatchRegistersByRoutes = $dispatchRegistersByVehicle->groupBy('route_id');
        $routeDispatches = collect([]);
        foreach ($dispatchRegistersByRoutes as $dispatchRegistersByRoute) {
            $routeDispatches->push((object)[
                'route' => $dispatchRegistersByRoute->first()->route,
                'roundTrips' => $dispatchRegistersByRoute->count(),
                'dispatchRegisters' => $dispatchRegistersByRoute
            ]);
        }

        return $routeDispatches;
    }

    /**
     * Export excel by Vehicle option
     *
     * @param Company $company
     * @param $dateReport
     * @param string $routeReport
     * @param string $vehicleReport
     * @param bool $completedTurns
     * @return Collection
     * @internal param $roundTripDispatchRegisters
     */
    public function buildManagementReport(Company $company, $dateReport, $routeReport = 'all', $vehicleReport = 'all', $completedTurns = true)
    {
        $dispatchRegistersByVehicles = $this->allByVehicles($company, $dateReport, $routeReport, $vehicleReport, $completedTurns);

        $managementReport = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $vehicle = Vehicle::find($vehicleId);
            $lastLocation = $vehicle->lasLocation($dateReport);
            
            $consolidatedDispatches = $this->getConsolidatedDataDispatches($dispatchRegistersByVehicle, $company);
            $consolidatedWithMaxSpeed = $consolidatedDispatches->sortByDesc('maxSpeed')->first();

            $eventsVehicleReports = $consolidatedDispatches->where('hasEvent', true);
            $totalOffRoads = $eventsVehicleReports->sum('totalOffRoads');
            $totalSpeeding = $eventsVehicleReports->sum('totalSpeeding');

            $company = $vehicle->company;
            $driver = $dispatchRegistersByVehicle->last()->driver;

            $totalRouteTime = $dispatchRegistersByVehicle->pluck('routeTime')->reduce(function ($carry, $item) use (&$cont) {
                return StrTime::addStrTime($carry ? $carry : "00:00:00", $item);
            });

            $totalRoundTrips = $this->getTotalRoundTrips($dispatchRegistersByVehicle, $company);
            $dispatchRoutes = $this->getRouteTurns($dispatchRegistersByVehicle);

            $managementReport->push((object)[
                'vehicle' => $vehicle,
                'dateReport' => $dateReport,
                'totalRoundTrips' => $totalRoundTrips,
                'dispatchRoutes' => $dispatchRoutes,
                'totalRouteTime' => $totalRouteTime,
                'totalOffRoads' => $totalOffRoads,
                'totalSpeeding' => $totalSpeeding,
                'maxSpeed' => $consolidatedWithMaxSpeed->maxSpeed ?? '',
                'maxSpeedTime' => $consolidatedWithMaxSpeed->maxSpeedTime ?? '',
                'mileage' => $lastLocation->current_mileage ?? 0,
                'driver' => $driver
            ]);
        }
        return $managementReport;
    }

    /**
     * Export excel by Vehicle option
     *
     * @param Company $company
     * @param string $routeReport
     * @param string $vehicleReport
     * @param bool $onlyActive
     * @return Collection
     * @internal param $roundTripDispatchRegisters
     */
    public function buildCurrentVehicleStatusReport(Company $company = null, $routeReport = 'all', $vehicleReport = 'all', $onlyActive = true)
    {
        $currentVehicleStatusReport = collect([]);
        if ($company) {
            $vehicles = $company->activeVehicles;

            foreach ($vehicles as $vehicle) {
                $dispatcherVehicle = $vehicle->dispatcherVehicle;
                $currentLocation = $vehicle->currentLocation;
                $currentDispatchRegister = $currentLocation->currentDispatchRegister ?? null;
                $vehicleStatus = $currentLocation->vehicleStatus ?? null;

                $currentVehicleStatusReport->push((object)[
                    'vehicle' => $vehicle,
                    'dispatcherVehicle' => $dispatcherVehicle,
                    'currentDispatchRegister' => $currentDispatchRegister,
                    'currentLocation' => $currentLocation,
                    'vehicleStatus' => $vehicleStatus,
                ]);
            }
        }

        return $currentVehicleStatusReport;
    }
}