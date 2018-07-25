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
        Log::useDailyFiles(storage_path() . '/logs/api/pcw-proprietary-report.log', 1);
        $data = collect(['success' => true, 'message' => '']);

        $now = Carbon::now();
        $proprietary = Proprietary::where('id', $request->get('id'))->get()->first();

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
                    $currentDispatchRegister = $dispatchRegisters->last();
                    $route = $currentDispatchRegister->route;
                    $arrivalTime = explode('.', $currentDispatchRegister->arrival_time)[0];

                    $recorder = CounterByRecorder::reportByVehicle($vehicle->id, $dispatchRegisters, true);
                    $sensor = CurrentSensorPassengers::where('placa', $vehicle->plate)->get()->first();
                    $timeSensor = explode('.', $sensor->timeStatus)[0]; // TODO Change column when table contador is migrated

                    $passengersByRecorder = $recorder->report->passengers;
                    $passengersBySensor = $sensor->passengers;

                    //dd($dispatchRegisters->toArray());

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
                        'dispatchRegisters' => $dispatchRegisters
                    ]);

                    Log::info("Send passenger report (passengersBySensor: $passengersBySensor, passengersByRecorder: $passengersByRecorder) to proprietary: $proprietary->id: " . $proprietary->fullName());
                }
            }
            $data->put('data', $reports);
        } else {
            $data->put('success', false);
            $data->put('message', __('Proprietary not found in platform'));
        }

        //$data = json_decode('{"success":true,"message":"","data":[{"prop":true,"vn":"338","pr":294,"ps":27,"tr":"16:22:45","ts":"18:07:21","rn":"RUTA 6","rr":4,"rt":53},{"prop":true,"vn":"356","pr":306,"ps":98,"tr":"17:05:32","ts":"18:07:22","rn":"RUTA 3","rr":4,"rt":33},{"prop":true,"vn":"361","pr":361,"ps":0,"tr":"16:44:23","ts":"00:05:06","rn":"RUTA 6","rr":4,"rt":55}]}',true);

        return response()->json($data);
    }
}