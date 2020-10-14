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
use App\Models\Vehicles\Speeding;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Psy\Util\Str;

class SpeedingService
{
    /**
     * Generate detailed speeding report for all vehicles of a company in a date
     *
     * @param Company $company
     * @param $dateReport
     * @return Collection
     */
    function speedingByVehiclesReport(Company $company, $dateReport)
    {
        $speedingByVehiclesReport = collect([]);
        $speedingByVehicles = $this->speedingByVehicles($this->allSpeeding($company, $dateReport));

        foreach ($speedingByVehicles as $vehicleId => $speedingByVehicle) {
            $speedingByVehicleByRoute = $this->groupByFirstSpeedingEventByRoute($speedingByVehicle);
            $speedingByVehiclesReport->put($vehicleId, [
                'vehicle' => Vehicle::find($vehicleId),
                'speedingByVehicle' => $speedingByVehicle,
                'totalSpeeding' => $speedingByVehicle->count(),
                'speedingByRoutes' => (object)[
                    'speedingByRoute' => $speedingByVehicleByRoute,
                    'totalSpeedingByRoutes' => $speedingByVehicleByRoute->sum(function ($route) {
                        return count($route);
                    })
                ]
            ]);
        }

        return $speedingByVehiclesReport;
    }

    /**
     * Get all Speeding of a company, date, route (optional) and vehicle (optional)
     *
     * @param Company $company
     * @param $initialDate
     * @param $finalDate
     * @param null $routeReport
     * @param null $vehicleReport
     * @return Location[]|Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    function allSpeeding(Company $company, $initialDate, $finalDate, $routeReport = null, $vehicleReport = null)
    {
        $initialDate = trim($initialDate);
        $finalDate = trim($finalDate);
        $allSpeeding = Location::whereBetween('date', [$initialDate, $finalDate])->withSpeeding();

        if ($routeReport == 'all' || !$routeReport) {
            $vehicles = $company->vehicles();
            if ($vehicleReport != 'all') {
                $vehicles = $vehicles->where('id', $vehicleReport);
            }

            $allSpeeding = $allSpeeding->whereIn('vehicle_id', $vehicles->get()->pluck('id'));
        } else {
            $dispatchRegisters = DispatchRegister::completed()->whereCompanyAndDateRangeAndRouteIdAndVehicleId($company, $initialDate, $finalDate, $routeReport, $vehicleReport)
                ->select('id')
                ->get();
            $allSpeeding = $allSpeeding->whereIn('dispatch_register_id', $dispatchRegisters->pluck('id'));
        }

        $allSpeeding = $allSpeeding
            ->with(['vehicle', 'dispatchRegister', 'addressLocation'])
            ->orderBy('date')->get();

        $allSpeeding = $allSpeeding
            ->filter(function (Location $s) use ($initialDate, $finalDate) {
                $time = $s->date->toTimeString();
                $initialTime = collect(explode(' ', $initialDate))->get(1);
                $finalTime = collect(explode(' ', $finalDate))->get(1);

                return $time >= $initialTime && $time <= $finalTime;
            })->sortBy('id');

        return $allSpeeding;
    }

    /**
     * Get all speeding of a dispatch register
     *
     * @param DispatchRegister $dispatchRegister
     * @return Collection
     */
    function byDispatchRegister(DispatchRegister $dispatchRegister)
    {
        $allSpeedingByDispatchRegister = Location::withSpeeding()
            ->where('dispatch_register_id', $dispatchRegister->id)
            ->orderBy('date')
            ->get();

        return $this->groupByFirstSpeedingEvent($allSpeedingByDispatchRegister);
    }

    /**
     * Groups all speeding by vehicle and first event
     *
     * @param \Illuminate\Database\Eloquent\Collection|Location[] $allSpeeding
     * @return Collection
     */
    function speedingByVehicles($allSpeeding)
    {
        $allSpeedingReportByVehicles = $allSpeeding->groupBy('vehicle_id');

        $speedingByVehicles = collect([]);
        foreach ($allSpeedingReportByVehicles as $vehicleId => $speedingByVehicle) {
            $speedingEvents = $this->groupByFirstSpeedingEvent($speedingByVehicle);
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
    function groupByFirstSpeedingEventByRoute($speedingByVehicle)
    {
        $speedingEvents = $this->groupByFirstSpeedingEvent($speedingByVehicle);

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
    public function groupByFirstSpeedingEvent($speedingByVehicle)
    {
        $speedingByVehicle = $speedingByVehicle->where('speeding', true);

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