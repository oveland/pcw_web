<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\CurrentLocation;
use App\CurrentSensorPassengers;
use App\DispatchRegister;
use App\Proprietary;
use App\Services\API\Apps\Contracts\APIInterface;
use App\Services\PCWSeatSensorGualas;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PCWProprietaryService implements APIInterface
{
    //
    public static function serve(Request $request): JsonResponse
    {
        $action = $request->get('action');
        if ($action) {
            switch ($action) {
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

    public static function trackPassengers(Request $request): JsonResponse
    {
        $data = collect(['success' => true, 'message' => '']);

        $proprietary = Proprietary::where('id', $request->get('id'))->get()->first();

        if ($proprietary) {
            $assignedVehicles = $proprietary->assignedVehicles;

            $reports = collect([]);
            foreach ($assignedVehicles as $assignation) {
                $vehicle = $assignation->vehicle;
                $passengersReportByVehicle = self::makeVehicleReport($vehicle);
                if ($passengersReportByVehicle->isNotEmpty()) {
                    $reports->push($passengersReportByVehicle);
                }
            }
            $data->put('currentPassengersReports', $reports);
            $data->put('proprietaryName', $proprietary->simpleName);
        } else {
            $data->put('success', false);
            $data->put('message', __('Proprietary not found in platform'));
        }


        //dd($data);
        return response()->json($data);
    }

    /**
     * @param Vehicle $vehicle
     * @return \Illuminate\Support\Collection|object $passengersReportByVehicle
     */
    public static function makeVehicleReport(Vehicle $vehicle)
    {
        $now = Carbon::now();
        $passengersReportByVehicle = collect([]);
        $allDispatchRegisters = DispatchRegister::where('date', $now->toDateString())
            ->with('vehicle')
            ->with('route')
            ->where('vehicle_id', $vehicle->id)
            ->where(function ($query) {
                return $query->where('status', DispatchRegister::COMPLETE)->orWhere('status', DispatchRegister::IN_PROGRESS);
            })
            ->orderBy('departure_time')
            ->get();

        $completedDispatchRegisters = $allDispatchRegisters->filter(function ($dispatchRegister, $key) {
            return $dispatchRegister->complete();
        });

        $lastDispatchRegister = $allDispatchRegisters->last();

        $currentSensor = CurrentSensorPassengers::where('placa', $vehicle->plate)->get()->first(); // TODO Change column  placa when table contador is migrated
        $timeSensor = explode('.', $currentSensor->timeStatus)[0];
        $timeSensorRecorder = explode('.', $currentSensor->timeSensorRecorder)[0];

        $currentLocation = CurrentLocation::where('vehicle_id', $vehicle->id)->get()->first();

        /* ONLY FOR VEHICLES WITH SEATING SENSOR COUNTER */
        $seatingStatus = PCWSeatSensorGualas::getSeatingStatusFromHex($currentSensor->seating, $vehicle->plate);

        if ($completedDispatchRegisters->isNotEmpty()) {
            $counterByRecorder = CounterByRecorder::reportByVehicle($vehicle->id, $completedDispatchRegisters, true);
            $timeRecorder = $counterByRecorder->report->timeRecorder;

            $counterBySensor = CounterBySensor::reportByVehicle($vehicle->id, $completedDispatchRegisters);

            $totalByRecorder = $counterByRecorder->report->passengers;

            $totalSensorRealTime = $lastDispatchRegister->complete() ? 0 : ($currentSensor->pas_tot - $lastDispatchRegister->initial_sensor_counter);
            $passengersBySensor = $counterBySensor->report->passengersBySensor + $totalSensorRealTime;

            $totalSensorRecorderRealTime = $lastDispatchRegister->complete() ? 0 : ($currentSensor->des_p1 - $lastDispatchRegister->initial_sensor_recorder);
            $totalBySensorRecorder = $counterBySensor->report->passengersBySensorRecorder + $totalSensorRecorderRealTime;

            $passengersReportByVehicle = collect((object)[
                'totalByRecorder' => $totalByRecorder,
                'totalBySensorRecorder' => $totalBySensorRecorder,
                'totalBySensor' => $passengersBySensor,
                'dispatchRegister' => $lastDispatchRegister ? $lastDispatchRegister->getAPIFields() : null,
                'vehicle' => $vehicle->getAPIFields($currentLocation),
                'currentLocation' => $currentLocation->getAPIFields(),
                'timeSensor' => $timeSensor,
                'timeSensorRecorder' => $timeSensorRecorder,
                'timeRecorder' => $timeRecorder,
                'historyReport' => self::makeHistoryReport($vehicle, $counterByRecorder, $counterBySensor),
                'seatingStatus' => $seatingStatus
            ]);
        } else {
            $passengersReportByVehicle = collect((object)[
                'totalByRecorder' => 0,
                'totalBySensorRecorder' => 0,
                'totalBySensor' => $currentSensor->pas_tot,
                'dispatchRegister' => $lastDispatchRegister ? $lastDispatchRegister->getAPIFields() : null,
                'vehicle' => $vehicle->getAPIFields($currentLocation),
                'currentLocation' => $currentLocation->getAPIFields(),
                'timeSensor' => $timeSensor,
                'timeSensorRecorder' => $timeSensorRecorder,
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
     * @return \Illuminate\Support\Collection
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
                'dispatchRegister' => $dispatchRegister,
                'vehicle' => $vehicle,
                'timeSensor' => $dispatchRegister->arrivalTime,
                'timeRecorder' => $dispatchRegister->arrivalTime,
            ]);
        }

        return $historyReport;
    }
}
