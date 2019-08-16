<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 10/10/2018
 * Time: 10:16 PM
 */

namespace App\Services\Reports\Routes;


use App\Models\Company\Company;
use App\Models\Routes\DispatcherVehicle;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Collection;

class SpeedingService
{
    /**
     * Generate detailed speeding report for all vehicles of a company in a date
     *
     * @param Company $company
     * @param $dateReport
     * @param $typeReport
     * @param null $routeReport
     * @return object
     */
    function buildSpeedingReport(Company $company, $dateReport, $typeReport, $routeReport = null)
    {
        $speedingByVehicles = $this->speedingByVehicles($this->allSpeeding($company, $dateReport, $routeReport));

        $report = $speedingByVehicles;
        // TODO: Uncomment a write some code when type report is used on the view report
        /*switch ($typeReport) {
            case 'group':
                foreach ($speedingByVehicles as $speedingByVehicle){
                    $speedingByVehicleByRoute = self::groupByFirstSpeedingEventByRoute($speedingByVehicle);
                    $report = $speedingByVehicleByRoute;
                }
                break;
            default:
                break;
        }*/

        return (object)[
            'company' => $company,
            'companyReport' => $company->id,
            'routeReport' => $routeReport,
            'dateReport' => $dateReport,
            'typeReport' => $typeReport,
            'report' => $report,
            'total' => $report->count()
        ];
    }

    /**
     * Get all Speeding of a company and date
     *
     * @param Company $company
     * @param $dateReport
     * @param null $routeReport
     * @return Location[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    function allSpeeding(Company $company, $dateReport, $routeReport = null)
    {
        $vehicles = $company->userVehicles($routeReport);

        return Location::witSpeeding()
            ->whereBetween('date', [$dateReport, "$dateReport 23:59:59"])
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->with('vehicle')
            ->get();
    }

    /**
     * Get all speeding of a dispatch register
     *
     * @param DispatchRegister $dispatchRegister
     * @return Collection
     */
    function speedingByDispatchRegister(DispatchRegister $dispatchRegister)
    {
        $allSpeedingByDispatchRegister = Location::witSpeeding()
            ->where('dispatch_register_id', $dispatchRegister->id)
            ->orderBy('date')
            ->get();

        return self::groupByFirstSpeedingEvent($allSpeedingByDispatchRegister);
    }

    /**
     * Groups all speeding by vehicle and first event
     *
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Location[] $allSpeeding
     * @return Collection
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
     * @return Collection
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
     * @return Collection
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
                $speedingEvents->push($speeding);
            }
            $lastSpeeding = $speeding;
        }

        return $speedingEvents->sortBy('date');
    }
}