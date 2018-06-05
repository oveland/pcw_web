<?php

namespace App\Http\Controllers\API\Apps;

use App\Company;
use App\CurrentLocation;
use App\CurrentLocationReport;
use App\Http\Controllers\API\interfaces\APIInterface;
use App\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppMyRouteController extends Controller implements APIInterface
{
    //
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
                        'lng' => $currentLocation->longitude
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
