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
use App\Speeding;
use App\Vehicle;

class SpeedingService
{
    /**
     * Generate detailed speeding report for all vehicles of a company in a date
     *
     * @param Company $company
     * @param $dateReport
     * @return \Illuminate\Support\Collection
     */
    function speedingByVehiclesReport(Company $company, $dateReport)
    {
        $speedingByVehiclesReport = collect([]);
        $speedingByVehicles = $this->speedingByVehicles($this->allSpeeding($company, $dateReport));

        foreach ($speedingByVehicles as $vehicleId => $speedingByVehicle) {
            $speedingByVehicleByRoute = self::groupByFirstSpeedingEventByRoute($speedingByVehicle);
            $speedingByVehiclesReport->put($vehicleId, [
                'vehicle' => Vehicle::find($vehicleId),
                'speedingByVehicle' => $speedingByVehicle,
                'totalSpeeding' => $speedingByVehicle->count(),
                'speedingByRoutes' => (object)[
                    'speedingByRoute' => $speedingByVehicleByRoute,
                    'totalSpeedingByRoutes' => $speedingByVehicleByRoute->sum(function ($route) { return count($route); })
                ]
            ]);
        }

        return $speedingByVehiclesReport;
    }

    /**
     * Get all Speeding of a company and date
     *
     * @param Company $company
     * @param $dateReport
     * @return Speeding[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    function allSpeeding(Company $company, $dateReport)
    {
        return Speeding::where('date', $dateReport)
            ->whereIn('vehicle_id', $company->vehicles->pluck('id'))
            ->get();
    }

    /**
     * Get all speeding of a dispatch register
     *
     * @param DispatchRegister $dispatchRegister
     * @return \Illuminate\Support\Collection
     */
    function speedingByDispatchRegister(DispatchRegister $dispatchRegister)
    {
        $allSpeedingByDispatchRegister = Speeding::where('dispatch_register_id', $dispatchRegister->id)
            ->orderBy('date')
            ->get();

        return self::groupByFirstSpeedingEvent($allSpeedingByDispatchRegister);
    }

    /**
     * Groups all speeding by vehicle and first event
     *
     * @param \Illuminate\Database\Eloquent\Collection|\App\Location[] $allSpeeding
     * @return \Illuminate\Support\Collection
     */
    function speedingByVehicles($allSpeeding)
    {
        $allSpeedingReportByVehicles = $allSpeeding->groupBy('vehicle_id');

        $speedingByVehicles = collect([]);
        foreach ($allSpeedingReportByVehicles as $vehicleId => $speedingByVehicle) {
            $speedingEvents = self::groupByFirstSpeedingEvent($speedingByVehicle);
            if (count($speedingEvents)) $speedingByVehicles->put($vehicleId, $speedingEvents);
        }

        return $speedingByVehicles;
    }


    /**
     * Extract first event of the all speeding and group it by route
     *
     * @param $speedingByVehicle
     * @return \Illuminate\Support\Collection
     */
    static function groupByFirstSpeedingEventByRoute($speedingByVehicle)
    {
        $speedingEvents = self::groupByFirstSpeedingEvent($speedingByVehicle);

        $speedingEventsByRoutes = $speedingEvents->where('dispatch_register_id', '<>', null)
            ->sortBy(function ($speeding, $key) {
                return $speeding->dispatchRegister->route->name;
            })
            ->groupBy(function ($speeding, $key) {
                return $speeding->dispatchRegister->route->id;
            });

        return $speedingEventsByRoutes;
    }

    /**
     * Extract first event of the all speeding
     *
     * @param $speedingByVehicle
     * @return \Illuminate\Support\Collection
     */
    public static function groupByFirstSpeedingEvent($speedingByVehicle)
    {

        $speedingEvents = collect([]);
        if (!count($speedingByVehicle)) return $speedingEvents;

        $lastSpeeding = null;
        foreach ($speedingByVehicle as $speeding) {
            if ($lastSpeeding) {
                if ($speeding->time->diff($lastSpeeding->time)->format('%H:%I:%S') > '00:05:00') {
                    $speedingEvents->push($speeding);
                }
            } else {
                //$speedingEvents->push($speeding);
            }
            $lastSpeeding = $speeding;
        }

        return $speedingEvents->sortBy('date');
    }
}