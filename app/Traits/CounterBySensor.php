<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 30/07/2018
 * Time: 10:21 PM
 */

namespace App\Traits;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Collection;

trait CounterBySensor
{
    /**
     * @param Collection $dispatchRegisters
     * @param null $classifyByRoute
     * @return object
     */
    static function report($dispatchRegisters, $classifyByRoute = null)
    {
        $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id');

        $report = array();
        $issues = array();
        $lastDispatchRegister = $dispatchRegisters->sortBy('date')->last();
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $totalByVehicle = self::totalByVehicle($vehicleId, $dispatchRegistersByVehicle);
            $report[$vehicleId] = $totalByVehicle->report;
            $totalByVehicle->issues->isNotEmpty() ? $issues[$vehicleId] = $totalByVehicle->issues : null;
        }
        return (object)[
            'report' => collect($report)->sortBy(function ($report) {
                return $report->vehicle->number;
            }),
            'issues' => collect($issues),
            'lastVehicleNumber' => $lastDispatchRegister ? $lastDispatchRegister->vehicle->number : '',
            'lastDriverName' => $lastDispatchRegister ? $lastDispatchRegister->driver_code . ($lastDispatchRegister->driver ? ' | ' . $lastDispatchRegister->driver->fullName() : '') : '',
        ];


    }

    static function totalByVehicle($vehicleId, $dispatchRegistersByVehicle)
    {
        $history = collect([]);
        $issues = collect([]);
        $vehicle = Vehicle::find($vehicleId);
        $dispatchRegistersByVehicle = $dispatchRegistersByVehicle->sortBy(function ($dr) {
            return "$dr->date-" . $dr->vehicle->number . "$dr->departure_time";
        });

        $totalBySensor = 0;
        $totalAllBySensor = 0;
        $totalByRecorder = 0;
        $totalBySensorRecorder = 0;
        $totalByPassengerVisual = 0;

        $totalPassengers = 0;
        $firstStartRecorder = 0;
        $lastEndRecorder = 0;
        $firstDispatchRegisterByVehicle = $dispatchRegistersByVehicle->first();
        $lastDispatchRegisterByVehicle = $dispatchRegistersByVehicle->last();

        $lastDispatchRegister = null;
        if ($firstDispatchRegisterByVehicle) {
            $firstStartRecorder = $firstDispatchRegisterByVehicle->start_recorder;
            $lastEndRecorder = $lastDispatchRegisterByVehicle->end_recorder;

            $startRecorder = $firstStartRecorder;

            foreach ($dispatchRegistersByVehicle as $dispatchRegister) {
                $totalBySensorByRoundTrip = $dispatchRegister->passengersBySensor;
                $totalBySensor += $totalBySensorByRoundTrip;

                $TotalpassengerVisualRoundTrip = (int) $dispatchRegister->getObservation('end_recorder')->value;
                $totalByPassengerVisual += $TotalpassengerVisualRoundTrip;

                $totalAllBySensorByRoundTrip = $dispatchRegister->passengersBySensorTotal;
                $totalAllBySensor += $totalAllBySensorByRoundTrip;

                $totalBySensorRecorderByRoundTrip = $dispatchRegister->passengersBySensorRecorder;
                $totalBySensorRecorder += $totalBySensorRecorderByRoundTrip;

//                $totalByRecorderByRoundTrip = $totalBySensorRecorderByRoundTrip;
//                $totalByRecorder += $totalByRecorderByRoundTrip;

                $drObs = $dispatchRegister->getObservation('end_recorder');
                $passengersByRoundTrip = $drObs && $vehicle->company_id == 39 ? $drObs->value : 0;
                $endRecorder = 0;

//                $startRecorder = $dispatchRegister->start_recorder > 0 ? $dispatchRegister->start_recorder : $startRecorder;
                $startRecorder = $dispatchRegister->start_recorder;

                if ($dispatchRegister->complete()) {
                    $endRecorder = $dispatchRegister->end_recorder;

                    // Recorder has 6 digits
                    if ($startRecorder > 999900 && $endRecorder < 500) {
                        $endRecorder = 999999 + $endRecorder;
                    }

                    $passengersByRoundTrip = $passengersByRoundTrip ?: $endRecorder - $startRecorder;
                    $totalPassengers += $passengersByRoundTrip;
                }

                $totalByRecorderByRoundTrip = $passengersByRoundTrip;
                $totalByRecorder += $totalByRecorderByRoundTrip;

                if (!$firstStartRecorder) $firstStartRecorder = $startRecorder;

                $driver = $dispatchRegister->driver;

                $history->put($dispatchRegister->id, (object)[
                    'passengersByRoundTrip' => $passengersByRoundTrip,
                    'totalPassengersByRoute' => $totalPassengers,

                    'totalBySensorByRoundTrip' => $totalBySensorByRoundTrip,
                    'totalBySensorByRoute' => $totalBySensor,

                    'TotalpassengerVisualRoundTrip' => $TotalpassengerVisualRoundTrip,
                    'totalByPassengerVisual' => $totalByPassengerVisual,

                    'totalAllBySensorByRoundTrip' => $totalAllBySensorByRoundTrip,
                    'totalAllBySensorByRoute' => $totalAllBySensor,

                    'totalByRecorderByRoundTrip' => $totalByRecorderByRoundTrip,
                    'totalByRecorderByRoute' => $totalByRecorder,

                    'totalBySensorRecorderByRoundTrip' => $totalBySensorRecorderByRoundTrip,
                    'totalBySensorRecorderByRoute' => $totalBySensorRecorder,


                    /* ---------- Dispatch Register info ---------- */

                    'startRecorder' => $startRecorder,
                    'endRecorder' => $endRecorder,

                    'routeId' => $dispatchRegister->route->id,
                    'routeName' => $dispatchRegister->route->name,
                    'roundTrip' => $dispatchRegister->round_trip,
                    'turn' => $dispatchRegister->turn,
                    'departureTime' => $dispatchRegister->departure_time,
                    'arrivalTime' => $dispatchRegister->arrival_time,
                    'statusDispatchRegister' => $dispatchRegister->status,
                    'dispatchRegisterIsComplete' => $dispatchRegister->complete(),
                    'driver' => $dispatchRegister->driverName(),
                    'departureFringe' => $dispatchRegister->departureFringe,
                    'arrivalFringe' => $dispatchRegister->arrivalFringe,

                    'dispatchRegister' => $dispatchRegister
                ]);

//                $issues = self::processIssues($vehicleId, $issues, $startRecorder, $endRecorder, $passengersByRoundTrip, $dispatchRegister, $lastDispatchRegister);
                // The next Start recorder is the last end recorder
//                $startRecorder = $endRecorder > 0 ? $endRecorder : $startRecorder;

                // Save the last dispatch register
                if ($dispatchRegister->complete()) $lastDispatchRegister = $dispatchRegister;
            }
        }

        $startRecorder = $history->isNotEmpty() ? $history->first()->startRecorder : 0;
        if (!$firstStartRecorder) $firstStartRecorder = $startRecorder;

        $totalByVehicle = (object)[
            'report' => (object)[
                'vehicle' => $vehicle,

                'passengersBySensorRecorder' => $totalBySensorRecorder, // Passengers by Sensor recorder
                'passengersBySensor' => $totalBySensor,                 // Passengers by Sensor
                'totalByPassengerVisual' => $totalByPassengerVisual,
                'passengersAllBySensor' => $totalAllBySensor,                 // Passengers by Sensor

                'start_recorder' => $firstStartRecorder,
                'firstStartRecorder' => $firstStartRecorder,
                'lastEndRecorder' => $lastEndRecorder,

                'passengers' => $totalPassengers,
                'passengersByRecorder' => $totalPassengers,

                'timeRecorder' => $lastDispatchRegister ? $lastDispatchRegister->arrival_time : '--:--:--',
                'history' => $history,
                'issue' => $issues->first(),

                'lastDriverName' => $lastDispatchRegister ? $lastDispatchRegister->driver_code . ($lastDispatchRegister->driver ? ' | ' . $lastDispatchRegister->driver->fullName() : '') : '',
            ],
            'issues' => $issues
        ];
        return $totalByVehicle;
    }

    /**
     * @param $vehicleId
     * @param $issues
     * @param $startRecorder
     * @param $endRecorder
     * @param $passengersByRoundTrip
     * @param $dispatchRegister
     * @param $lastDispatchRegister
     * @return object
     */
    public static function processIssues($vehicleId, $issues, $startRecorder, $endRecorder, $passengersByRoundTrip, $dispatchRegister, $lastDispatchRegister)
    {
        $issueField = null;
        $badStartRecorder = false;
        if ($dispatchRegister->complete()) {
            if ($startRecorder <= 0 && false) { // Commented with false, this logic is controlled by trigger on registrodespacho table
                $issueField = __('Start Recorder');
            } else if ($endRecorder <= 0) {
                $issueField = __('End Recorder');
            } else if ($passengersByRoundTrip > 1000) {
                $issueField = __('A high count');
            } else if ($passengersByRoundTrip < 0) {
                $issueField = __('A negative count');
            } else if ($lastDispatchRegister && $lastDispatchRegister->end_recorder > 0 && $startRecorder < $lastDispatchRegister->end_recorder
                && $startRecorder != 66600 && $dispatchRegister->id != 1624106 && $dispatchRegister->id != 1633048 && $dispatchRegister->id != 1941775 &&
                $dispatchRegister->id != 2114035 && $dispatchRegister->id != 2222105
            ) {
                $start = $startRecorder;
                $endLast = $lastDispatchRegister && $lastDispatchRegister->end_recorder > 0 ? $lastDispatchRegister->end_recorder : 0;
                $issueField = __('A Start Recorder less than the last End Recorder') . " ($start < $endLast)." . $dispatchRegister->route->name . ', ' . __('Turn') . " $dispatchRegister->turn";
                $badStartRecorder = true;
            }/* else if ($passengersByRoundTrip < config('counter.recorder.threshold_low_count')) {
                $issueField = __('Low count') . ' < ' . config('counter.recorder.threshold_low_count');
            }*/
        }


        if ($issueField) {
            $issues->push((object)[
                'field' => $issueField,
                'route_id' => $dispatchRegister->route_id,
                'vehicle_id' => $vehicleId,
                'start_recorder' => $startRecorder,
                'end_recorder' => $endRecorder,
                'lastDispatchRegister' => $lastDispatchRegister,
                'bad_start_recorder' => $badStartRecorder,
                'passengers' => $passengersByRoundTrip,
                'dispatchRegister' => $dispatchRegister,
            ]);
        }

        return $issues;
    }

    /**
     * @param $vehicleId
     * @param $dispatchRegistersByVehicle
     * @param null $classifyByRoute
     * @return object
     */
    public static function reportByVehicle($vehicleId, $dispatchRegistersByVehicle, $classifyByRoute = null)
    {
        return self::totalByVehicle($vehicleId, $dispatchRegistersByVehicle);
    }
}
