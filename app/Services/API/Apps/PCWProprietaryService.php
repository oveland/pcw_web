<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\Models\Vehicles\CurrentLocation;
use App\Models\Passengers\CurrentSensorPassengers;
use App\Models\Routes\DispatchRegister;
use App\Models\Proprietaries\Proprietary;
use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\Reports\Passengers\SeatDistributionService;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Models\Vehicles\Vehicle;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PCWProprietaryService implements APIAppsInterface
{
    //
    public static function serve(Request $request): JsonResponse
    {
        $action = $request->get('action');
        if ($action) {
            switch ($action) {
                case 'get-vehicle':
                    return self::getVehicle($request);
                    break;
                case 'get-vehicles':
                    return self::getVehicles($request);
                    break;
                case 'track-passengers':
                    return self::trackPassengers($request);
                    break;
                default:
                    return response()->json([
                        'error' => true,
                        'message' => 'Invalid action serve'
                    ]);
                    break;
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No action found!'
            ]);
        }
    }

    public static function getVehicle(Request $request): JsonResponse
    {
        $data = collect(['success' => true, 'message' => '']);

        $vehicle = Vehicle::find($request->get('vehicle'));
        $proprietary = Proprietary::find($request->get('proprietary'));

        if ($proprietary) {
            self::checkSession($proprietary);

            if ($vehicle) {
                $data->put('vehicle', $vehicle->getAPIFields());
            } else {
                $data->put('success', false);
                $data->put('message', __('Vehicle not found in platform'));
            }
        } else {
            $data->put('success', false);
            $data->put('message', __('Proprietary not found in platform'));
        }

        return response()->json($data);
    }

    public static function getVehicles(Request $request): JsonResponse
    {
        $data = collect(['success' => true, 'message' => '']);
        $proprietary = Proprietary::find($request->get('proprietary'));

        if ($proprietary) {
            self::checkSession($proprietary);

            if ($proprietary->user) {
                $assignedVehicles = $proprietary->assignedVehicles();

                $vehicles = collect([]);
                foreach ($assignedVehicles as $vehicle) {
                    $vehicles->push($vehicle->getAPIFields());
                }
                $data->put('vehicles', $vehicles);
                $data->put('proprietaryName', $proprietary->simpleName);
            } else {
                $data->put('success', false);
                if (!$proprietary->user) {
                    $data->put('message', __('Proprietary not have assigned vehicles'));
                }
            }
        } else {
            $data->put('success', false);
            $data->put('message', __('Proprietary not found in platform'));
        }

        return response()->json($data);
    }

    public static function trackPassengers(Request $request): JsonResponse
    {
        $data = collect(['success' => true, 'message' => '']);

        $proprietary = Proprietary::find($request->get('proprietary'));

        if ($proprietary) {
            $vehicle = Vehicle::find($request->get('vehicle'));
            self::checkSession($proprietary);
            $passengersReportByVehicle = self::makeVehicleReport($vehicle);
            $data->put('currentPassengersReport', $passengersReportByVehicle);
        } else {
            $data->put('success', false);
            $data->put('message', __('Proprietary not found in platform'));
        }

        return response()->json($data);
    }

    /**
     * @param Vehicle $vehicle
     * @return Collection|object $passengersReportByVehicle
     */
    public static function makeVehicleReport(Vehicle $vehicle)
    {
        $now = Carbon::now();
        $allDispatchRegisters = DispatchRegister::active()
            ->where('date', $now->toDateString())
            ->where('vehicle_id', $vehicle->id)
            ->orderBy('departure_time')
            ->with('vehicle')
            ->with('route')
            ->get();

        $completedDispatchRegisters = $allDispatchRegisters->filter(function ($dispatchRegister, $key) {
            return $dispatchRegister->complete();
        });

        $lastDispatchRegister = $allDispatchRegisters->last();
        $currentSensor = CurrentSensorPassengers::whereVehicle($vehicle);
        $currentSensor = isset($currentSensor->passengers) ? $currentSensor : null;
        $currentLocation = CurrentLocation::whereVehicle($vehicle);

        /* ONLY FOR VEHICLES WITH SEATING SENSOR COUNTER */

        $topology = $vehicle->seatTopology();

        $seatingStatus = $topology->getSeatingStatusFromHex($currentSensor ? $currentSensor->seating : '000000');

        if ($completedDispatchRegisters->isNotEmpty()) {
            $counterByRecorder = CounterByRecorder::reportByVehicle($vehicle->id, $completedDispatchRegisters, true);
            $counterBySensor = CounterBySensor::reportByVehicle($vehicle->id, $completedDispatchRegisters);

            $totalByRecorder = $counterByRecorder->report->passengers;

            $totalSensorRealTime = $lastDispatchRegister->complete() && $currentSensor ? 0 : ($currentSensor->sensorCounter - $lastDispatchRegister->initial_sensor_counter);
            $passengersBySensor = $counterBySensor->report->passengersBySensor + $totalSensorRealTime;

            $totalSensorRecorderRealTime = $lastDispatchRegister->complete() && $currentSensor ? 0 : ($currentSensor->sensorRecorderCounter - $lastDispatchRegister->initial_sensor_recorder);
            $totalBySensorRecorder = $counterBySensor->report->passengersBySensorRecorder + $totalSensorRecorderRealTime;

            $passengersReportByVehicle = collect((object)[
                'totalByRecorder' => $totalByRecorder,
                'totalBySensorRecorder' => $totalBySensorRecorder,
                'totalBySensor' => $passengersBySensor,
                'dispatchRegister' => $lastDispatchRegister ? $lastDispatchRegister->getAPIFields() : null,
                'vehicle' => $vehicle->getAPIFields($currentLocation),
                'currentLocation' => $currentLocation->getAPIFields(),
                'timeSensor' => $currentSensor ? $currentSensor->timeStatus : '00:00:00',
                'timeSensorRecorder' => $currentSensor ? $currentSensor->timeSensorRecorder : '00:00:00',
                'timeRecorder' => $counterByRecorder->report->timeRecorder,
                'historyReport' => self::makeHistoryReport($vehicle, $counterByRecorder, $counterBySensor),
                'seatingStatus' => $seatingStatus
            ]);
        } else {
            $passengersReportByVehicle = collect((object)[
                'totalByRecorder' => 0,
                'totalBySensorRecorder' => 0,
                'totalBySensor' => $currentSensor ? $currentSensor->pas_tot : 0,
                'dispatchRegister' => $lastDispatchRegister ? $lastDispatchRegister->getAPIFields() : null,
                'vehicle' => $vehicle->getAPIFields($currentLocation),
                'currentLocation' => $currentLocation->getAPIFields(),
                'timeSensor' => $currentSensor ? $currentSensor->timeStatus : '00:00:00',
                'timeSensorRecorder' => $currentSensor ? $currentSensor->timeSensorRecorder : '00:00:00',
                'timeRecorder' => '00:00:00',
                'historyReport' => [],
                'seatingStatus' => $seatingStatus
            ]);
        }

        return $passengersReportByVehicle;
    }

    /**
     * @param Vehicle $vehicle
     * @param $counterByRecorder
     * @param $counterBySensor
     * @return Collection
     */
    public static function makeHistoryReport(Vehicle $vehicle, $counterByRecorder, $counterBySensor)
    {
        $historyReport = collect([]);
        $historyReportByRecorder = $counterByRecorder->report->history;
        $historyReportBySensor = $counterBySensor->report->history;

        foreach ($historyReportByRecorder as $historyRecorder) {
            $dispatchRegister = $historyRecorder->dispatchRegister;
            $historySensor = $historyReportBySensor[$dispatchRegister->id];

            $historyReport->push((object)[
                'totalByRecorder' => $historyRecorder->passengersByRoundTrip,
                'totalBySensorRecorder' => $historySensor->totalBySensorRecorderByRoundTrip,
                'totalBySensor' => $historySensor->totalBySensorByRoundTrip,
                'dispatchRegister' => $dispatchRegister->getAPIFields(),
                'vehicle' => $vehicle->getAPIFields(),
                'timeSensor' => $dispatchRegister->arrivalTime,
                'timeRecorder' => $dispatchRegister->arrivalTime,
            ]);
        }

        return $historyReport;
    }

    public static function checkSession(Proprietary $proprietary)
    {
        $user = $proprietary->user;
        if ($user && Auth::guest()) {
            Auth::login($user);
        }
    }
}
