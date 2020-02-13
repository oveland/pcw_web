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
                case 'get-daily-taking-report':
                    return self::getDailyTakingReport($request);
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

    public static function checkSession(Proprietary $proprietary)
    {
        $user = $proprietary->user;
        if ($user && Auth::guest()) {
            Auth::login($user);
        }
    }
}
