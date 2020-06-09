<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 18/11/2017
 * Time: 4:21 PM
 */

namespace App\Traits;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Collection;

trait CounterByRecorder
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
            $totalByVehicle = self::totalByVehicle($vehicleId, $dispatchRegisters, $dispatchRegistersByVehicle, $classifyByRoute);
            $report[$vehicleId] = $totalByVehicle->report;
            $totalByVehicle->issues->isNotEmpty() ? $issues[$vehicleId] = $totalByVehicle->issues : null;
        }

        return (object)[
            'report' => collect($report)->sortBy(function ($report) {
                return $report->vehicle->number;
            }),
            'issues' => collect($issues),
            'lastVehicleNumber' => $lastDispatchRegister ? $lastDispatchRegister->vehicle->number : '',
            'lastDriverName' => $lastDispatchRegister ? $lastDispatchRegister->driver_code . ($lastDispatchRegister->driver ? ' | '.$lastDispatchRegister->driver->fullName() : '') : '',
        ];
    }

    static function totalByVehicle($vehicleId, $dispatchRegisters, $dispatchRegistersByVehicle, $classifyByRoute = null)
    {
        $history = collect([]);
        $issues = collect([]);
        $vehicle = Vehicle::find($vehicleId);

        if ($vehicle->countAllFromSensorRecorder()) return CounterBySensor::totalByVehicle($vehicleId, $dispatchRegistersByVehicle);

        $dispatchRegistersByVehicle = $dispatchRegistersByVehicle->sortBy('departure_time');

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
                $passengersByRoundTrip = 0;
                $endRecorder = 0;
                if( $dispatchRegister->complete() ){
                    $endRecorder = $dispatchRegister->end_recorder;
                    $startRecorder = $dispatchRegister->start_recorder > 0 ? $dispatchRegister->start_recorder : $startRecorder;


                    if ($classifyByRoute) {
                        $lastDispatchRegister = DispatchRegister::active()->where('vehicle_id', $vehicleId)
                            ->where('date', '=', $dispatchRegister->date)
                            ->where('id', '<', $dispatchRegister->id)
                            ->orderByDesc('id')
                            ->limit(1)->get()->first();
                    }

                    /* For change route between prev and current dispatch registers when there are prev registers */
                    if ($lastDispatchRegister && $lastDispatchRegister->route->id != $dispatchRegister->route->id) {
                        if ($classifyByRoute) {
                            $endRecorderByOtherRoutes = $lastDispatchRegister->end_recorder;
                        } else {
                            $endRecorderByOtherRoutes = $dispatchRegisters->where('vehicle_id', $vehicleId)
                                    ->where('id', '<', $dispatchRegister->id)
                                    ->where('id', '>=', $lastDispatchRegister->id ?? 0)
                                    ->last()->end_recorder ?? null;
                        }

                        $startRecorder = (($endRecorderByOtherRoutes > 0 && abs($startRecorder - $endRecorderByOtherRoutes) > 1000)) ? $endRecorderByOtherRoutes : $startRecorder;
                    } else if (abs($endRecorder - $startRecorder) > 1000) {
                        $startRecorder = 0; // For search a properly value in the next logic
                    }

                    if ($startRecorder == 0) {
                        $startRecorder = DispatchRegister::active()
                                ->where('vehicle_id', $vehicleId)
                                ->where('date', '=', $dispatchRegister->date)
                                ->where('id', '<', $dispatchRegister->id)
                                ->orderByDesc('id')
                                ->limit(1)->get()->first()
                                ->end_recorder ?? 0;

                        if ($startRecorder == 0) {
                            $startRecorder = DispatchRegister::active()
                                    ->where('vehicle_id', $vehicleId)
                                    ->where('date', '<', $dispatchRegister->date)
                                    ->orderByDesc('id')
                                    ->limit(1)->get()->first()
                                    ->end_recorder ?? 0;
                        }
                    }

                    // Recorder has 6 digits
                    if ($startRecorder > 999900 && $endRecorder < 500) {
                        $endRecorder = 999999 + $endRecorder;
                    }

                    $passengersByRoundTrip = $endRecorder - $startRecorder;
                    $totalPassengers += $passengersByRoundTrip;
                }

                if (!$firstStartRecorder) $firstStartRecorder = $startRecorder;

                $driver = $dispatchRegister->driver;

                $history->put($dispatchRegister->id, (object)[
                    'passengersByRoundTrip' => $passengersByRoundTrip,
                    'totalPassengersByRoute' => $totalPassengers,

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
                    'driver' => $driver ? $driver->fullName() : __('Not assigned'),
                    'departureFringe' => $dispatchRegister->departureFringe,
                    'arrivalFringe' => $dispatchRegister->arrivalFringe,

                    'dispatchRegister' => $dispatchRegister
                ]);

                $issues = self::processIssues($vehicleId, $issues, $startRecorder, $endRecorder, $passengersByRoundTrip, $dispatchRegister, $lastDispatchRegister);
                // The next Start recorder is the last end recorder
                $startRecorder = $endRecorder > 0 ? $endRecorder : $startRecorder;

                // Save the last dispatch register
                if( $dispatchRegister->complete() )$lastDispatchRegister = $dispatchRegister;
            }
        }

        $totalByVehicle = (object)[
            'report' => (object)[
                'vehicle' => $vehicle,

                'start_recorder' => $firstStartRecorder,
                'firstStartRecorder' => $firstStartRecorder,
                'lastEndRecorder' => $lastEndRecorder,

                'passengers' => $totalPassengers,
                'passengersByRecorder' => $totalPassengers,

                'timeRecorder' => $lastDispatchRegister ? $lastDispatchRegister->arrival_time : '--:--:--',
                'history' => $history,
                'issue' => $issues->first(),

                'lastDriverName' => $lastDispatchRegister ? $lastDispatchRegister->driver_code . ($lastDispatchRegister->driver ? ' | '.$lastDispatchRegister->driver->fullName() : '') : '',
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
        if($dispatchRegister->complete()){
            if ($startRecorder <= 0) {
                $issueField = __('Start Recorder');
            } else if ($endRecorder <= 0) {
                $issueField = __('End Recorder');
            } else if ($passengersByRoundTrip > 1000) {
                $issueField = __('A high count');
            } else if ($passengersByRoundTrip < 0) {
                $issueField = __('A negative count');
            } else if ($lastDispatchRegister && $lastDispatchRegister->end_recorder > 0 && $startRecorder < $lastDispatchRegister->end_recorder) {
                $issueField = __('A Start Recorder less than the last End Recorder') . ' ' . $dispatchRegister->route->name . ', ' . __('Turn') . " $dispatchRegister->turn";
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
        return self::totalByVehicle($vehicleId, $dispatchRegistersByVehicle, $dispatchRegistersByVehicle, $classifyByRoute);
    }
}