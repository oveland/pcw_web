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
    static function report($dispatchRegisters,$classifyByRoute = null)
    {
        $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id');

        $report = array();
        $issues = array();
        foreach ($dispatchRegistersByVehicles as $vehicle_id => $dispatchRegistersByVehicle) {
            $totalByVehicle = self::totalByVehicle($vehicle_id, $dispatchRegisters, $dispatchRegistersByVehicle, $classifyByRoute);
            $report[$vehicle_id] = $totalByVehicle->report;
            $totalByVehicle->issues->isNotEmpty() ? $issues[$vehicle_id] = $totalByVehicle->issues : null;
        }

        return (object)[
            'report' => collect($report)->sortBy(function ($report) {
                return $report->vehicle->number;
            }),
            'issues' => collect($issues)
        ];
    }

    static function totalByVehicle($vehicle_id, $dispatchRegisters, $dispatchRegistersByVehicle,$classifyByRoute = null)
    {
        $vehicle = Vehicle::find($vehicle_id);

        $dispatchRegistersByVehicle = $dispatchRegistersByVehicle->sortBy('departure_time');
        $totalPassengersByVehicle = 0;
        $issues = collect([]);

        $firstDispatchRegisterByVehicle = $dispatchRegistersByVehicle->first();
        $start_recorder = $firstDispatchRegisterByVehicle->start_recorder;
        $first_start_recorder = $start_recorder;

        $lastDispatchRegister = null;
        $last_route_id = null;
        $index = 0;
        foreach ($dispatchRegistersByVehicle as $dispatchRegister) {
            $end_recorder = $dispatchRegister->end_recorder;
            $start_recorder = $dispatchRegister->start_recorder > 0 ? $dispatchRegister->start_recorder : $start_recorder;


            if( $classifyByRoute ){
                $lastDispatchRegister = PassengersDispatchRegister::where('vehicle_id', $vehicle_id)
                        ->where('date', '=', $dispatchRegister->date)
                        ->where('id', '<', $dispatchRegister->id)
                        ->orderByDesc('id')
                        ->limit(1)->get()->first();
            }

            /* For change route between prev and current dispatch registers when there are prev registers */
            if ($lastDispatchRegister && $lastDispatchRegister->route->id != $dispatchRegister->route->id) {
                if( $classifyByRoute ){
                    $endRecorderByOtherRoutes = $lastDispatchRegister->end_recorder;
                }else{
                    $endRecorderByOtherRoutes = $dispatchRegisters->where('vehicle_id', $vehicle_id)
                            ->where('id', '<', $dispatchRegister->id)
                            ->where('id', '>=', $lastDispatchRegister->id ?? 0)
                            ->last()->end_recorder ?? null;
                }

                $start_recorder = ( ($endRecorderByOtherRoutes > 0 && abs($start_recorder - $endRecorderByOtherRoutes) > 1000)) ? $endRecorderByOtherRoutes : $start_recorder;
            }
            else if(abs($end_recorder - $start_recorder) > 1000){
                $start_recorder = 0; // For search a properly value in the next logic
            }

            if ($start_recorder == 0) {
                $start_recorder = PassengersDispatchRegister::where('vehicle_id', $vehicle_id)
                    ->where('date', '=', $dispatchRegister->date)
                    ->where('id', '<', $dispatchRegister->id)
                    ->orderByDesc('id')
                    ->limit(1)->get()->first()
                    ->end_recorder ?? 0;

                if ($start_recorder == 0) {
                    $start_recorder = PassengersDispatchRegister::where('vehicle_id', $vehicle_id)
                        ->where('date', '<', $dispatchRegister->date)
                        ->orderByDesc('id')
                        ->limit(1)->get()->first()
                        ->end_recorder;
                }
            }

            // Recorder has 6 digits
            if( $start_recorder > 999900 && $end_recorder < 500 ){
                $end_recorder = 999999 + $end_recorder;
            }

            $passengersByRoundTrip = $end_recorder - $start_recorder;
            $totalPassengersByVehicle += $passengersByRoundTrip;

            $issueField = null;
            $badStartRecorder = false;
            if ($start_recorder <= 0) {
                $issueField = __('Start Recorder');
            } else if ($end_recorder <= 0) {
                $issueField = __('End Recorder');
            } else if ($passengersByRoundTrip > 1000) {
                $issueField = __('A high count');
            } else if ($passengersByRoundTrip < 0) {
                $issueField = __('A negative count');
            } else if ($lastDispatchRegister && $lastDispatchRegister->end_recorder > 0 &&  $start_recorder < $lastDispatchRegister->end_recorder ) {
                $issueField = __('A Start Recorder less than the last End Recorder').' '. $dispatchRegister->route->name.', '.__('Turn')." $dispatchRegister->turn";
                $badStartRecorder = true;
            }

            if ($issueField) {
                $issues->push((object)[
                    'field' => $issueField,
                    'route_id' => $dispatchRegister->route_id,
                    'vehicle_id' => $vehicle_id,
                    'start_recorder' => $start_recorder,
                    'end_recorder' => $end_recorder,
                    'lastDispatchRegister' => $lastDispatchRegister,
                    'bad_start_recorder' => $badStartRecorder,
                    'passengers' => $passengersByRoundTrip,
                    'dispatchRegister' => $dispatchRegister
                ]);
            }

            // The next Start recorder is the last end recorder
            $start_recorder = $end_recorder > 0 ? $end_recorder : $start_recorder;

            // Save the last dispatch register
            $lastDispatchRegister = $dispatchRegister;
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