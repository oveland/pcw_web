<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 18/11/2017
 * Time: 4:21 PM
 */

namespace App\Traits;


use App\DispatchRegister;
use App\PassengersDispatchRegister;
use App\Vehicle;
use Carbon\Carbon;

trait CounterByRecorder
{
    static function report($dispatchRegisters)
    {
        $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id');

        $report = array();
        $issues = array();
        foreach ($dispatchRegistersByVehicles as $vehicle_id => $dispatchRegistersByVehicle) {
            $totalByVehicle = self::totalByVehicle($vehicle_id, $dispatchRegisters, $dispatchRegistersByVehicle);
            $report[] = $totalByVehicle->report;
            $totalByVehicle->issues->isNotEmpty() ? $issues[$vehicle_id] = $totalByVehicle->issues : null;
        }

        return (object)[
            'report' => collect($report)->sortBy(function ($report) {
                return $report->vehicle->number;
            }),
            'issues' => collect($issues)
        ];
    }

    static function totalByVehicle($vehicle_id, $dispatchRegisters, $dispatchRegistersByVehicle)
    {
        $vehicle = Vehicle::find($vehicle_id);
        $totalPassengersByVehicle = 0;
        $issues = collect([]);

        $firstDispatchRegisterByVehicle = $dispatchRegistersByVehicle->first();
        $start_recorder = $firstDispatchRegisterByVehicle->start_recorder;
        $first_start_recorder = $start_recorder;

        $lastDispatchRegister = null;
        $last_route_id = null;
        $index = 0;
        foreach ($dispatchRegistersByVehicle as $dispatchRegister) {
            $start_recorder = $dispatchRegister->start_recorder > 0 ? $dispatchRegister->start_recorder : $start_recorder;

            /* For change route on prev dispatch register */
            if ($index > 0) {
                if ($last_route_id && $last_route_id != $dispatchRegister->route->id) {
                    $endRecorderByOtherRoutes = $dispatchRegisters
                            ->where('vehicle_id', $vehicle_id)
                            ->where('id', '<', $dispatchRegister->id)
                            ->where('id', '>=', $lastDispatchRegister->id ?? 0)
                            ->last()->end_recorder ?? null;

                    $start_recorder = ($endRecorderByOtherRoutes > $start_recorder || ($endRecorderByOtherRoutes > 0 && ($start_recorder - $endRecorderByOtherRoutes) > 1000)) ? $endRecorderByOtherRoutes : $start_recorder;
                }
            } else {
                if ($start_recorder == 0) {
                    $start_recorder = PassengersDispatchRegister::
                    where('vehicle_id', $vehicle_id)
                        ->where('date', '<', $dispatchRegister->date)
                        ->orderByDesc('end_recorder')
                        ->limit(1)->get()->first()
                        ->end_recorder;
                }
            }

            $end_recorder = $dispatchRegister->end_recorder;
            $passengersByRoundTrip = $end_recorder - $start_recorder;

            $totalPassengersByVehicle += $passengersByRoundTrip;

            $issueField = null;
            if ($start_recorder <= 0) {
                $issueField = __('Start Recorder');
            } else if ($end_recorder <= 0) {
                $issueField = __('End Recorder');
            } else if ($passengersByRoundTrip > 1000) {
                $issueField = __('A high count');
            } else if ($passengersByRoundTrip < 0) {
                $issueField = __('A negative count');
            }

            if ($issueField) {
                $issues->push((object)[
                    'field' => $issueField,
                    'route_id' => $dispatchRegister->route_id,
                    'vehicle_id' => $vehicle_id,
                    'start_recorder' => $start_recorder,
                    'end_recorder' => $end_recorder,
                    'passengers' => $passengersByRoundTrip,
                    'dispatchRegister' => $dispatchRegister
                ]);

                if ($vehicle->number == 349) {
                    //dd($issues);
                }
            }
            $start_recorder = $end_recorder > 0 ? $end_recorder : $start_recorder;

            $lastDispatchRegister = $dispatchRegister;
            $last_route_id = $dispatchRegister->route->id;
            $index++;
        }

        $totalByVehicle = (object)[
            'report' => (object)[
                'vehicle' => $vehicle,
                'start_recorder' => $first_start_recorder,
                'passengers' => $totalPassengersByVehicle,
                'issue' => $issues->first()
            ],
            'issues' => $issues
        ];

        return $totalByVehicle;
    }
}