<?php

namespace App\Services\Reports\Routes;


use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Speeding;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SpeedingService
{
    /**
     * Get all events of a company, date, route (optional) and vehicle (optional)
     *
     * @param Company $company
     * @param $initialDate
     * @param $finalDate
     * @param null $routeReport
     * @param null $vehicleReport
     * @return Location[]|Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    function all(Company $company, $initialDate, $finalDate, $routeReport = null, $vehicleReport = null)
    {
        $initialDate = trim($initialDate);
        $finalDate = trim($finalDate);
        $all = Speeding::whereBetween('date', [$initialDate, $finalDate])->withSpeeding();

        if ($routeReport == 'all' || !$routeReport) {
            $vehicles = $company->vehicles();
            if ($vehicleReport != 'all') {
                $vehicles = $vehicles->where('id', $vehicleReport);
            }

            $all = $all->whereIn('vehicle_id', $vehicles->get()->pluck('id'));
        } else {
            $dispatchRegisters = DispatchRegister::completed()->whereCompanyAndDateRangeAndRouteIdAndVehicleId($company, $initialDate, $finalDate, $routeReport, $vehicleReport)
                ->select('id')
                ->get();
            $all = $all->whereIn('dispatch_register_id', $dispatchRegisters->pluck('id'));
        }

        return $all
            ->with(['vehicle', 'dispatchRegister', 'addressLocation'])
            ->orderBy('date')
            ->get()
            ->filter(function (Location $s) use ($initialDate, $finalDate) {
                $time = $s->date->toTimeString();
                $initialTime = collect(explode(' ', $initialDate))->get(1);
                $finalTime = collect(explode(' ', $finalDate))->get(1);

                return $time >= $initialTime && $time <= $finalTime;
            })->sortBy('id');
    }

    /**
     * Groups all events by vehicle
     *
     * @param \Illuminate\Database\Eloquent\Collection|Location[] $all
     * @return Collection
     */
    function groupByVehicles($all, $onlyMax = false)
    {
        $eventsByVehicles = collect([]);
        foreach ($all->groupBy('vehicle_id') as $vehicleId => $allByVehicle) {
            $events = $this->groupByEvent($allByVehicle);
            if (count($events)) $eventsByVehicles->put($vehicleId, $events);
        }

        if ($onlyMax) {
            $eventsByVehicles = $eventsByVehicles->mapWithKeys(function ($d, $vehicleId) {
                return [$vehicleId => collect($d)->sortByDesc('speed')->take(1)];
            });
        }

        return $eventsByVehicles;
    }

    /**
     * Extract first event of the all registers
     *
     * @param $allByVehicle
     * @return Collection
     */
    public function groupByEvent($allByVehicle)
    {
        $allByVehicle = $allByVehicle->where('speeding', true);

        $events = collect([]);
        if (!count($allByVehicle)) return $events;

        $last = null;
        foreach ($allByVehicle as $event) {
            if ($last) {
                if ($event->time->diff($last->time)->format('%H:%I:%S') > '00:05:00') {
                    $events->push($event);
                }
            } else {
                $events->push($event);
            }
            $last = $event;
        }

        return $events->sortBy('date');
    }
}