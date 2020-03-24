<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App;
use App\Models\Proprietaries\Proprietary;
use App\Services\API\Apps\Contracts\APIAppsInterface;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PCWProprietaryService implements APIAppsInterface
{
    //
    public static function serve(Request $request): JsonResponse
    {
        $action = $request->get('action');
        if ($action) {
            switch ($action) {
                case 'get-taking-daily-report':
                case 'get-daily-taking-report':
                    return self::getDailyTakingReport($request);
                    break;
                case 'get-consolidated-daily-report':
                    return self::getConsolidatedDailyReport($request);
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

    public static function getDailyTakingReport(Request $request)
    {
        $data = collect(['success' => true, 'message' => '']);
        $proprietary = Proprietary::find($request->get('proprietary'));
        self::checkSession($proprietary);
        $vehicleId = $request->get('vehicle');
        $date = $request->get('date');
        $beaService = App::makeWith('bea.service', ['company' => $proprietary->company_id]);

        $data->put('report', $beaService->getDailyReport($vehicleId, $date));

        return response()->json($data);
    }

    public static function getConsolidatedDailyReport(Request $request)
    {
        $data = collect(['success' => true, 'message' => '']);
        $proprietary = Proprietary::find($request->get('proprietary'));
        self::checkSession($proprietary);
        $date = $request->get('date');
        $type = $request->get('type');
        $beaService = App::makeWith('bea.service', ['company' => $proprietary->company_id]);

        $reportComplete = $beaService->getConsolidatedDailyReport($date, $type);
        $report = collect([]);

        $markWithOtherDiscounts = collect([]);
        $iteration = 0;
        foreach ($reportComplete->data as $d) {
            $iteration ++;
            $mark = $d->mark;
            $trajectory = $mark->trajectory;
            $liquidationTurn = $d->liquidationTurn;
            $liquidationDetails = $d->liquidationDetails;
            $turn = $mark->turn;
            $vehicle = (object) $turn->vehicle;

            $otherDiscounts = collect($liquidationDetails->otherDiscounts)->sum('value');

            if($markWithOtherDiscounts->get($mark->liquidation_id)){
                $otherDiscounts = 0;
            }else{
                $markWithOtherDiscounts->put($mark->liquidation_id, $otherDiscounts);
            }

            $totalTaken = $mark->taken ? $liquidationTurn->totalDispatch - $otherDiscounts : 0;

            $report->push([
                'iteration' => $iteration,
                'trajectoryName' => $trajectory->name,
                'initialTime' => $mark->initialTime,
                'finalTime' => $mark->finalTime,
                'vehicleNumber' => $vehicle->number,
                'totalGrossBEA' => $mark->totalGrossBEA,
                'passengersBEA' => $mark->passengersBEA,
                'totalTurn' => $liquidationTurn->totalTurn,
                'otherDiscounts' => $otherDiscounts,
                'totalLiquidated' => $liquidationTurn->totalDispatch,
                'totalTaken' => $totalTaken,
                'difference' => $liquidationTurn->totalDispatch - $otherDiscounts - $totalTaken,
                'liquidated' => $mark->liquidated ? true : '',
                'taken' => $mark->taken ? true : '',
            ]);
        }

        $data->put('report', $report);

        return response()->json($data);
    }

    public static function checkSession(Proprietary $proprietary)
    {
        $user = $proprietary->user;
        if ($user && Auth::guest()) {
            Auth::login($user);
        }
    }
}
