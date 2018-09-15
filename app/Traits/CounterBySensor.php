<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 30/07/2018
 * Time: 10:21 PM
 */

namespace App\Traits;


use App\Vehicle;

trait CounterBySensor
{
    static function report($dispatchRegisters)
    {
        $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id');

        $report = array();
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $totalByVehicle = self::totalByVehicle($vehicleId, $dispatchRegistersByVehicle);
            $report[$vehicleId] = $totalByVehicle->report;
        }

        return (object)[
            'report' => collect($report)->sortBy(function ($report) {
                return $report->vehicle->number;
            })
        ];
    }

    static function totalByVehicle($vehicleId, $dispatchRegistersByVehicle)
    {
        $vehicle = Vehicle::find($vehicleId);
        $dispatchRegistersByVehicle = $dispatchRegistersByVehicle->sortBy('departure_time');

        $history = collect([]);
        $issues = collect([]);

        $totalBySensor = 0;
        $totalByRecorder = 0;
        $totalBySensorRecorder = 0;

        $lastDispatchRegister = null;
        foreach ($dispatchRegistersByVehicle as $dispatchRegister) {
            $totalBySensorByRoundTrip = $dispatchRegister->passengersBySensor;
            $totalBySensor += $totalBySensorByRoundTrip;

            $totalBySensorRecorderByRoundTrip = $dispatchRegister->passengersBySensorRecorder;
            $totalBySensorRecorder += $totalBySensorRecorderByRoundTrip;

            $totalByRecorderByRoundTrip = $totalBySensorRecorderByRoundTrip;
            $totalByRecorder += $totalByRecorderByRoundTrip;

            $driver = $dispatchRegister->driver;

            $history->put($dispatchRegister->id, (object)[
                /*
                 * ----------------------------------------------------------
                 *      Nomenclature:
                 * ----------------------------------------------------------
                 *      Passengers prefix:       Default passengers counter
                 *
                 *      Sensor prefix:           Passengers by Sensor counter
                 *      Recorder prefix:         Passengers by Recorder counter
                 *      Sensor Recorder prefix:  Passengers by Sensor Recorder counter
                 *
                */

                'passengersByRoundTrip' => $totalBySensorRecorderByRoundTrip,
                'totalPassengersByRoute' => $totalBySensorRecorder,

                'totalBySensorByRoundTrip' => $totalBySensorByRoundTrip,
                'totalBySensorByRoute' => $totalBySensor,

                'totalByRecorderByRoundTrip' => $totalByRecorderByRoundTrip,
                'totalByRecorderByRoute' => $totalByRecorder,

                'totalBySensorRecorderByRoundTrip' => $totalBySensorRecorderByRoundTrip,
                'totalBySensorRecorderByRoute' => $totalBySensorRecorder,


                /* ---------- Dispatch Register info ---------- */

                'startRecorder' => $dispatchRegister->initialPassengersBySensorRecorder,
                'endRecorder' => $dispatchRegister->finalPassengersBySensorRecorder,

                'route' => $dispatchRegister->route->name,
                'roundTrip' => $dispatchRegister->round_trip,
                'turn' => $dispatchRegister->turn,
                'departureTime' => $dispatchRegister->departure_time,
                'arrivalTime' => $dispatchRegister->arrival_time,
                'statusDispatchRegister' => $dispatchRegister->status,
                'dispatchRegisterIsComplete' => $dispatchRegister->complete(),
                'driver' => $driver ? $driver->fullName() : __('Not assigned'),
                'departureFringe' => $dispatchRegister->departureFringe,
                'arrivalFringe' => $dispatchRegister->arrivalFringe,

                'dispatchRegister' => $dispatchRegister
            ]);

            // Save the last dispatch register
            if( $dispatchRegister->complete() )$lastDispatchRegister = $dispatchRegister;
        }

        $totalByVehicle = (object)[
            'report' => (object)[
                'vehicle' => $vehicle,

                'passengers' => $totalBySensorRecorder,                 // Default passengers

                'passengersByRecorder' => $totalBySensorRecorder,       // Passengers By Recorder is the sensor recorder
                'passengersBySensorRecorder' => $totalBySensorRecorder, // Passengers by Sensor recorder
                'passengersBySensor' => $totalBySensor,                 // Passengers by Sensor

                'start_recorder' => $history->isNotEmpty() ? $history->first()->startRecorder : 0,
                'timeRecorder' => $lastDispatchRegister?$lastDispatchRegister->arrival_time:'--:--:--',
                'history' => $history,
                'issue' => $issues->first()
            ],
            'issues' => $issues
        ];

        return $totalByVehicle;
    }

    /**
     * @param $vehicleId
     * @param $dispatchRegistersByVehicle
     * @return object
     */
    public static function reportByVehicle($vehicleId, $dispatchRegistersByVehicle)
    {
        return self::totalByVehicle($vehicleId, $dispatchRegistersByVehicle);
    }
}