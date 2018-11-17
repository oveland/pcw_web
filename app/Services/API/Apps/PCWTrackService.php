<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\PCWTime;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class PCWTrackService implements APIAppsInterface
{
    //
    public static function serve(Request $request): JsonResponse
    {
        $action = $request->get('action');
        if ($action) {
            switch ($action) {
                case 'track-route-times':
                    return self::trackRouteTime($request);
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

    public static function trackRouteTime(Request $request): JsonResponse
    {
        $data = collect(['success' => true, 'message' => '']);

        $vehicle = Vehicle::where('plate',$request->get('plate'))->get()->first();

        if( $vehicle ){
            $report = DB::select("
                SELECT v.plate vehicle_plate, v.number vehicle_number, r.name route_name, dr.round_trip round_trip, dr.turn, cr.date, cr.timed, cr.timep, cr.timem, dr.departure_time, (cr.timem::INTERVAL +dr.departure_time)::TIME time_m, (cr.timep::INTERVAL+dr.departure_time)::TIME time_p, 
                CASE WHEN ( abs(cr.status_in_minutes) <= 1 ) THEN 'ok' ELSE cr.status END status
                FROM current_reports cr
                  JOIN dispatch_registers dr ON (cr.dispatch_register_id = dr.id)
                  JOIN vehicles v ON (cr.vehicle_id = v.id)
                  JOIN Routes r ON (dr.route_id = r.id)
                WHERE v.plate = '$vehicle->plate' AND (current_timestamp - cr.date)::INTERVAL < '00:01:00'::INTERVAL
            ");

            if( count($report) && $report = $report[0] ){
                //Log::useDailyFiles(storage_path().'/logs/api/pcw-track-report.log',2);

                $dataMessage = collect([
                    'vp' => $report->vehicle_plate,
                    'vn' => $report->vehicle_number,
                    'rd' => PCWTime::toDateTimeString($report->date),
                    'rn' => $report->route_name,
                    'rr' => $report->round_trip,
                    'rt' => $report->turn,
                    'dpt' => $report->departure_time,
                    'sch' => $report->time_p,
                    'dif' => $report->timed,
                    'st' => $report->status
                ]);

                $data->put('data', $dataMessage);
                //Log::info( $dataMessage->toJson() );
            }else{
                $data->put('success', false);
                $data->put('message', __('No registers found'));
            }
        }
        else{
            $data->put('success', false);
            $data->put('message', __('Vehicle not found in platform'));
        }

        return response()->json($data);
    }
}