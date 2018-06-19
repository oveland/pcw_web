<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\CurrentSensorPassengers;
use App\DispatchRegister;
use App\Proprietary;
use App\Services\API\Apps\Contracts\APIInterface;
use App\Services\PCWTime;
use App\Traits\CounterByRecorder;
use App\Vehicle;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

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

        $now = Carbon::now();
        $proprietary = Proprietary::where('id', $request->get('id'))
            ->where('passenger_report_via_sms', true)
            ->get()->first();

        if ($proprietary) {
            $assignedVehicles = $proprietary->assignedVehicles;
            $reports = collect([]);
            foreach ($assignedVehicles as $assignation) {
                $vehicle = $assignation->vehicle;

                $dispatchRegisters = DispatchRegister::where('date', $now->toDateString())
                    ->where('vehicle_id', $vehicle->id)
                    ->completed()
                    ->orderBy('departure_time')
                    ->get();

                if (count($dispatchRegisters)) {
                    Log::useDailyFiles(storage_path() . '/logs/api/pcw-proprietary-report.log', 2);
                    $currentDispatchRegister = $dispatchRegisters->last();
                    $route = $currentDispatchRegister->route;
                    $arrivalTime = explode('.', $currentDispatchRegister->arrival_time)[0];

                    $recorder = CounterByRecorder::reportByVehicle($vehicle->id, $dispatchRegisters, true);
                    $sensor = CurrentSensorPassengers::where('placa', $vehicle->plate)->get()->first();
                    $timeSensor = explode('.', $sensor->hora_status)[0]; // TODO Change column when table contador is migrated

                    $passengersByRecorder = $recorder->report->passengers;
                    $passengersBySensor = $sensor->passengers;

                    $reports->push([
                        'prop' => true,                                 // Indicates that data SMS belongs to a proprietary
                        'vn' => $vehicle->number,
                        'pr' => $passengersByRecorder,                  // Passengers by Recorder
                        'ps' => $passengersBySensor,                    // Passengers by Sensor
                        'tr' => $arrivalTime,                           // Time by Recorder
                        'ts' => $timeSensor,                            // Time by Sensor,
                        'rn' => $route->name,                           // Route name,
                        'rr' => $currentDispatchRegister->round_trip,   // Route round trip,
                        'rt' => $currentDispatchRegister->turn,         // Route turn,
                    ]);

                    Log::info("Send passenger report to proprietary: $proprietary->id: " . $proprietary->fullName());
                }
            }
            $data->put('reports', $reports);
        } else {
            $data->put('success', false);
            $data->put('message', __('Proprietary not found in platform'));
        }

        return response()->json($data);
    }
}