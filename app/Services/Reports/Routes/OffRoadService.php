<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 10/10/2018
 * Time: 10:16 PM
 */

namespace App\Services\Reports\Routes;


use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;

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
     * @return Location[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
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
     * @return Location[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
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
     * @return Location[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
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
     * @param $offRoadsByVehicle
     * @return \Illuminate\Support\Collection
     */
    function groupByFirstOffRoad($offRoadsByVehicle){
        return self::groupByFirstOffRoadEvent($offRoadsByVehicle);
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
        $totalByGroup = 0;
        $firstOffRoadOnGroup = null;
        foreach ($offRoadsByVehicle as $offRoad) {
            if (!$lastOffRoad || $offRoad->date->diff($lastOffRoad->date)->format('%H:%I:%S') > '00:05:00') {
                $firstOffRoadOnGroup = $offRoad;
                $totalByGroup = 1;
            } else if ($totalByGroup > 0) {
                $totalByGroup++;
            }

            if ($totalByGroup > 3) {
                if( $firstOffRoadOnGroup->isTrueOffRoad() ){
                    $offRoadsEvents->push($firstOffRoadOnGroup);
                }
                $totalByGroup = 0;
            }

            $lastOffRoad = $offRoad;
        }
        return $offRoadsEvents;
    }
}