<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\Route;
use App\Services\API\Apps\Contracts\APIAppsInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyRouteService implements APIAppsInterface
{
    public static function serve(Request $request): JsonResponse
    {
        $action = $request->get('action');
        if ($action) {
            switch ($action) {
                case 'track-route-vehicles':
                    return self::trackVehicles($request);
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

    public static function trackVehicles(Request $request): JsonResponse
    {
        $data = collect(['success' => true, 'message' => '']);
        $route = Route::find(intval($request->get('route')));
        if ($route) {
            # Info Route
            $infoRoute = [
                'name' => $route->name
            ];
            $data->put('route', $infoRoute);

            # Info Dispatch Register
            $trackLocations = array();
            foreach ($route->currentLocations() as $currentLocation) {
                $dispatchRegister = $currentLocation->dispatchRegister;
                $vehicle = $dispatchRegister->vehicle;
                $trackLocations[] = [
                    'vehicle' => [
                        'id' => $vehicle->id,
                        'number' => $vehicle->number,
                        'plate' => $vehicle->plate,
                    ],
                    'location' => [
                        'lat' => $currentLocation->latitude,
                        'lng' => $currentLocation->longitude,
                        'orientation' => $currentLocation->orientation
                    ]
                ];
            }
            $data->put('trackLocations', $trackLocations);

        } else {
            $data->put('success', false);
            $data->put('message', 'Route not found');
        }
        return response()->json($data);
    }
}