<?php

namespace App\Services\Reports\Passengers;

use App\Http\Controllers\Utils\Geolocation;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Passengers\HistorySeat;
use App\Models\Routes\ControlPointsTariff;
use App\Models\Routes\DispatchRegister;

class OccupationService
{
    function getReportByDispatch(DispatchRegister $dispatchRegister, $thresholdKm = 0)
    {
        $route = $dispatchRegister->route;
        $routeCoordinates = Geolocation::getRouteCoordinates($route->url);

        $dispatchArrivalTime = $dispatchRegister->complete() ? $dispatchRegister->arrival_time : $dispatchRegister->arrival_time_scheduled;
        $dispatchArrivalTime = ($dispatchArrivalTime > "23:59:59") ? "23:59:59" : $dispatchArrivalTime;

        $initialTimeRange = StrTime::subStrTime($dispatchRegister->departure_time, '00:30:00');
        $finalTimeRange = StrTime::addStrTime(($dispatchRegister->canceled ? $dispatchRegister->time_canceled : $dispatchArrivalTime), '00:30:00');

        $historySeats = HistorySeat::where('plate', $dispatchRegister->vehicle->plate)
            ->where('date', '=', $dispatchRegister->date)
            ->where('dispatch_register_id', '=', $dispatchRegister->id)
            ->whereBetween('time', [$initialTimeRange, $finalTimeRange])
            ->get()->sortBy('active_time');

        $routeDistance = $dispatchRegister->route->distance * 1000;

        $cpT = $route->getControlPointsTariff();

        foreach ($historySeats as &$historySeat) {
            if ($historySeat->complete == 1) {
                //$busyDistance = $this->getBusyKm($historySeat, $routeCoordinates);
                $historySeat->active_km = $historySeat->active_km < $dispatchRegister->start_odometer ? 0 : ($historySeat->active_km - $dispatchRegister->start_odometer);

                $historySeat->inactive_km = $historySeat->inactive_km - $dispatchRegister->start_odometer;
                $historySeat->inactive_km = $historySeat->inactive_km < $routeDistance ? $historySeat->inactive_km : $routeDistance;

                $historySeat->busy_km = $historySeat->inactive_km - $historySeat->active_km;
            }

            $tariffs = $cpT->map(function (ControlPointsTariff $t) use ($historySeat) {
                $distanceToInitial = abs($historySeat->active_km - $t->fromControlPoint->distance_from_dispatch);
                $distanceToFinal = abs($historySeat->inactive_km - $t->toControlPoint->distance_from_dispatch);

                return (object)[
                    'id' => $t->id,
                    'difference' => $distanceToInitial + $distanceToFinal
                ];
            });

            if ($tariffs->count()) {
                $tariff = $cpT->where('id', $tariffs->sortBy('difference')->first()->id)->first();
                $historySeat->tariff = $tariff;
            }

        }

        $historySeats = $historySeats->sortBy('tariff.fromControlPoint.order');

        $truncateCounts = $historySeats->where('busy_km', '>=', $thresholdKm);
        $totalProduction = $truncateCounts->sum('tariff.value');

        return (object)compact(['historySeats', 'dispatchRegister', 'dispatchArrivalTime', 'thresholdKm', 'truncateCounts', 'totalProduction', 'totalPassengers' => $truncateCounts->count()]);
    }
}