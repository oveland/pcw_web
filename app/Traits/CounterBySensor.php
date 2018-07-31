<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 30/07/2018
 * Time: 10:21 PM
 */

namespace App\Traits;


use App\Vehicle;

trait CounterByRecorder
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
        $totalBySensor = 0;
        $totalBySensorRecorder = 0;

        $lastDispatchRegister = null;
        foreach ($dispatchRegistersByVehicle as $dispatchRegister) {
            $totalBySensorByRoundTrip = $dispatchRegister->passengersBySensor;
            $totalBySensor += $totalBySensorByRoundTrip;

            $totalBySensorRecorderByRoundTrip = $dispatchRegister->passengersBySensorRecorder;
            $totalBySensorRecorder += $totalBySensorRecorderByRoundTrip;

            $driver = $dispatchRegister->driver;

            $history->put($dispatchRegister->id, (object)[
                'totalBySensorByRoundTrip' => $totalBySensorByRoundTrip,
                'totalBySensorRecorderByRoundTrip' => $totalBySensorRecorderByRoundTrip,

                'route' => $dispatchRegister->route,
                'vehicle' => $dispatchRegister->route,
                'dispatchRegister' => $dispatchRegister,
                'driver' => $driver
            ]);
        }

        $totalByVehicle = (object)[
            'report' => (object)[
                'vehicle' => $vehicle,
                'passengersBySensor' => $totalBySensor,
                'passengersBySensorRecorder' => $totalBySensorRecorder,
                'history' => $history,
            ]
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