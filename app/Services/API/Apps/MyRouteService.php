<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\Models\Routes\Route;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyRouteService implements APIFilesInterface
{
    /**
     * @var Request
     */
    private $request;
    private $service;

    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');
    }

    public function serve(): JsonResponse
    {
        if ($this->service) {
            switch ($this->service) {
                case 'track-route-vehicles':
                    return self::trackVehicles();
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
                'message' => 'No service found!'
            ]);
        }
    }

    public function trackVehicles(): JsonResponse
    {
        $data = collect(['success' => true, 'message' => '']);
        $route = Route::find(intval($this->request->get('route')));
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