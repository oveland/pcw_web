<?php

namespace App\Services\Reports\Passengers;

use App\Http\Controllers\DriverDetailedController;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Passengers\HistorySeat;
use App\Models\Routes\ControlPoint;
use App\Models\Routes\ControlPointsTariff;
use App\Models\Routes\DispatchRegister;

class OccupationService
{
    function getReportByDispatch(DispatchRegister $dispatchRegister, $thresholdKm = 0)
    {
        $route = $dispatchRegister->route;

        $dispatchArrivalTime = $dispatchRegister->complete() ? $dispatchRegister->arrival_time : $dispatchRegister->arrival_time_scheduled;
        $dispatchArrivalTime = ($dispatchArrivalTime > "23:59:59") ? "23:59:59" : $dispatchArrivalTime;

        $initialTimeRange = StrTime::subStrTime($dispatchRegister->departure_time, '00:30:00');
        $finalTimeRange = StrTime::addStrTime(($dispatchRegister->canceled ? $dispatchRegister->time_canceled : $dispatchArrivalTime), '00:30:00');

        if ($dispatchRegister->vehicle->company_id == 14) {
            $historySeats = HistorySeat::where('plate', $dispatchRegister->vehicle->plate)
                ->where('date', '=', $dispatchRegister->date)
                ->where('dispatch_register_id', '=', $dispatchRegister->id)
                ->whereBetween('time', [$initialTimeRange, $finalTimeRange])
                ->get()->sortBy('active_time');
        } else {
            if ($route->distance_in_km > 100 || true) {
                $initialTimeRange = "$dispatchRegister->date $dispatchRegister->departure_time";
                $finalTimeRange = "$dispatchRegister->date_end $dispatchRegister->arrival_time";
            }
            $historySeats = HistorySeat::where('plate', $dispatchRegister->vehicle->plate)
//                ->where('date', '=', $dispatchRegister->date)
                ->where('dispatch_register_id', '=', $dispatchRegister->id)
                //->whereBetween('active_time', [$initialTimeRange, $finalTimeRange])
                ->get()->sortBy('seat');
        }
        $routeDistance = $dispatchRegister->route->distance * 1000;
        $controlPoints = $dispatchRegister->route->controlPoints;

        $defThresholdKm = $route->isLarge() ? 5000 : 0;
        $thresholdKm = $thresholdKm ?: $defThresholdKm;

        //$historySeats = $historySeats->sortBy('tariff.fromControlPoint.order');
        $historySeats = $historySeats->where('busy_km', '>', 0);
        $truncatedCounts = $historySeats->where('busy_km', $thresholdKm ? '>=' : '>', $thresholdKm);
        $totalProduction = $truncatedCounts->sum('tariff.value');

        $passengersStopsFICS = json_decode($dispatchRegister->getObservation('passengers_stops')->observation);

        $lastCp = null;
        $route = $dispatchRegister->route;
        $cpCounts = $controlPoints
            ->sortBy('order')
            ->mapWithKeys(function (ControlPoint $cp) use ($route, $truncatedCounts, $routeDistance, $passengersStopsFICS, &$lastCp) {
                $routeDT = $route->distance_threshold;
                $distanceCpFromDispatch = min($cp->distance_from_dispatch, $routeDistance);
                $distanceCpNextPoint = $cp->distance_next_point;

                $cpSeatsIn = $truncatedCounts
                    ->where('active_km', '<=', $distanceCpFromDispatch)
                    ->where('inactive_km', '>=', $distanceCpFromDispatch);
                $cpSeatsOn = $truncatedCounts
                    ->where('active_km', '<=', $cp->distance_from_dispatch)
                    ->where('inactive_km', '>=', $cp->distance_from_dispatch);
                $cpSeatsOut = $truncatedCounts
                    ->where('active_km', '<=', $distanceCpFromDispatch + $distanceCpNextPoint * 0.5)
                    ->where('inactive_km', '>=', $distanceCpFromDispatch + $distanceCpNextPoint * 0.5);

                if ($route->isLarge()) { // Toma en cuenta que para rutas largas los asientos se liberan en cada PdC
                    $cpSeatsIn = $truncatedCounts
                        ->where('inactive_km', '>', ($lastCp ? $lastCp->distance_from_dispatch : 0))
                        ->where('inactive_km', '<=', $distanceCpFromDispatch + $routeDT);
                    $cpSeatsOut = $truncatedCounts
                        ->where('active_km', '>=', $distanceCpFromDispatch)
                        ->where('active_km', '<=', $distanceCpFromDispatch + $distanceCpNextPoint);

                    // Filtra info en aquellos PdC por los que posiblemente no pasó el vehículo Tomando en cuenta el criterio de que en cada PdC debe haber un reinicio (inactive_km cercano al Km del PdC)
                    if(!$cpSeatsIn->count() && $cp->order > 0) {
                        $cpSeatsOut = collect([]);
                    }
                }

                $ascents = $cpSeatsOut->filter(function ($cpSeatOut) use ($cpSeatsIn) {
                    return !$cpSeatsIn->where('seat', $cpSeatOut->seat)->count();
                });

                $descents = $cpSeatsIn->filter(function ($cpSeatIn) use ($cpSeatsOut) {
                    return !$cpSeatsOut->where('seat', $cpSeatIn->seat)->count();
                });

                $ficsStops = null;
                $ficsStopCode = $cp->FICS ? $cp->FICS->fics_id : null;
                if ($ficsStopCode && isset($passengersStopsFICS->$ficsStopCode)) {
                    $ficsStops = $passengersStopsFICS->$ficsStopCode;
                }

                $lastCp = $cp;

                return [$cp->id => (object)[
                    'cp' => $cp,
                    'info' => (object)[
                        'in' => $cpSeatsIn,
                        'on' => $cpSeatsOn,
                        'out' => $cpSeatsOut,
                        'ascents' => $ascents->pluck('start_photo_id', 'seat'),
                        'descents' => $descents->pluck('end_photo_id', 'seat'),
                    ],
                    'count' => (object)[
                        'in' => $cpSeatsIn->count(),
                        'on' => $cpSeatsOn->count(),
                        'out' => $cpSeatsOut->count(),
                        'ascents' => $ascents->count(),
                        'descents' => $descents->count(),
                    ],
                    'ficsStops' => $ficsStops
                ]];
            });

        //
        // Process counts and Tariffs in history seats
        //

        $cpT = $route->getControlPointsTariff();
        foreach ($historySeats->sortBy('active_time') as &$historySeat) {
            if ($historySeat->complete == 1) {
                $historySeat->active_km = $historySeat->active_km < $dispatchRegister->start_odometer ? 0 : ($historySeat->active_km - $dispatchRegister->start_odometer);

                $historySeat->inactive_km = $historySeat->inactive_km - $dispatchRegister->start_odometer;
                $historySeat->inactive_km = min($historySeat->inactive_km, $routeDistance);

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

            $historySeat->cpFrom = $controlPoints
                ->sortBy('distance_from_dispatch')
                ->where('distance_from_dispatch', '<=', $historySeat->active_km)->last();

            $cpCountAscents = $cpCounts->get($historySeat->cpFrom->id)->info->ascents;
            $historySeat->isAscent = $cpCountAscents->get($historySeat->seat);

            $historySeat->cpTo = $controlPoints
                ->sortBy('distance_from_dispatch')
                ->where('distance_from_dispatch', '>=', $historySeat->inactive_km)->first();

            if ($historySeat->cpTo) {
                $cpCountDescents = $cpCounts->get($historySeat->cpTo->id)->info->descents;
                $historySeat->isDescent = $cpCountDescents->get($historySeat->seat);
            }
        }

        //dd('as');

        $totalAscents = $historySeats->where('isAscent', true)->count();
        $totalDescents = $historySeats->where('isDescent', true)->count();

        return (object)compact([
            'historySeats',
            'dispatchRegister',
            'controlPoints',
            'cpCounts',
            'dispatchArrivalTime',
            'thresholdKm',
            'truncatedCounts',
            'totalProduction',
            'totalAscents',
            'totalDescents',
            'passengersStopsFICS'
        ]);
    }
}