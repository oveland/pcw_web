<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 10/10/2018
 * Time: 10:16 PM
 */

namespace App\Services\pcwserviciosgps\reports\routes;


use App\Company;
use App\DispatchRegister;
use App\Location;
use App\OffRoad;
use App\Vehicle;

class OffRoadService
{
    /**
     * Generate detailed off road report for all vehicles of a company in a date
     *
     * @param Company $company
     * @param $dateReport
     * @return \Illuminate\Support\Collection
     */
    function offRoadByVehiclesReport(Company $company, $dateReport)
    {
        $offRoadByVehiclesReport = collect([]);
        $offRoadsByVehiclesByRoutes = $this->offRoadsByVehicles($this->allOffRoads($company, $dateReport));

        foreach ($offRoadsByVehiclesByRoutes as $vehicleId => $offRoadsByRoutes) {
            $offRoadByVehiclesReport->put($vehicleId, [
                'vehicle' => Vehicle::find($vehicleId),
                'offRoadsByRoutes' => $offRoadsByRoutes,
                'totalOffRoads' => $offRoadsByRoutes->sum(function ($route) {
                    return count($route);
                })
            ]);
        }

        return $offRoadByVehiclesReport;
    }

    /**
     * Get all offRoads of a company and date
     *
     * @param Company $company
     * @param $dateReport
     * @return OffRoad[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    function allOffRoads(Company $company, $dateReport)
    {
        return Location::whereBetween('date', [$dateReport . ' 00:00:00', $dateReport . ' 23:59:59'])
            ->where('off_road', true)
            ->whereIn('vehicle_id', $company->vehicles->pluck('id'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get all offRoads of a company and date
     *
     * @param Vehicle $vehicle
     * @param $dateReport
     * @return OffRoad[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    function offRoadsByVehicle(Vehicle $vehicle, $dateReport)
    {
        return Location::whereBetween('date', [$dateReport . ' 00:00:00', $dateReport . ' 23:59:59'])
            ->where('off_road', true)
            ->where('vehicle_id', $vehicle->id)
            ->orderBy('date')
            ->get();
    }

    /**
     * Get all offRoads of a dispatch register
     *
     * @param DispatchRegister $dispatchRegister
     * @return OffRoad[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    function offRoadsByDispatchRegister(DispatchRegister $dispatchRegister)
    {
        $allOffRoadsByDispatchRegister = Location::witOffRoads()
            ->where('dispatch_register_id', $dispatchRegister->id)
            ->orderBy('date')
            ->get();

        return self::groupByFirstOffRoadEvent($allOffRoadsByDispatchRegister);
    }

    /**
     * Groups all off roads by vehicle and first event
     *
     * @param $allOffRoads
     * @return \Illuminate\Support\Collection
     */
    function offRoadsByVehicles($allOffRoads)
    {
        $allOffRoadsByVehicles = $allOffRoads->groupBy('vehicle_id');

        $offRoadsByVehicles = collect([]);
        foreach ($allOffRoadsByVehicles as $vehicleId => $offRoadsByVehicle) {
            $offRoadsEvents = self::groupByFirstOffRoadByRoute($offRoadsByVehicle);
            if (count($offRoadsEvents)) $offRoadsByVehicles->put($vehicleId, $offRoadsEvents);
        }

        return $offRoadsByVehicles;
    }

    /**
     * Extract first event of the all off roads and group it by route
     *
     * @param $offRoadsByVehicle
     * @return \Illuminate\Support\Collection
     */
    static function groupByFirstOffRoadByRoute($offRoadsByVehicle)
    {
        $offRoadsEvents = self::groupByFirstOffRoadEvent($offRoadsByVehicle);

        $offRoadsEventsByRoutes = $offRoadsEvents
            ->sortBy(function ($offRoad, $key) {
                return $offRoad->dispatchRegister->route->name;
            })
            ->groupBy(function ($offRoad, $key) {
                return $offRoad->dispatchRegister->route->id;
            });

        return $offRoadsEventsByRoutes;
    }

    /**
     * Extract first event of the all off roads
     *
     * @param $offRoadsByVehicle
     * @return \Illuminate\Support\Collection
     */
    static function groupByFirstOffRoadEvent($offRoadsByVehicle)
    {
        $offRoadsEvents = collect([]);
        if (!count($offRoadsByVehicle)) return $offRoadsEvents;

        $lastOffRoad = null;
        foreach ($offRoadsByVehicle as $offRoad) {
            if ($lastOffRoad) {
                if ($offRoad->date->diff($lastOffRoad->date)->format('%H:%I:%S') > '00:05:00') {
                    $offRoadsEvents->push($offRoad);
                }
            } else {
                $offRoadsEvents->push($offRoad);
            }
            $lastOffRoad = $offRoad;
        }

        return $offRoadsEvents;
    }
}