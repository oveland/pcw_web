<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\Models\Vehicles\Vehicle;
use App\Services\API\Apps\Contracts\APIAppsInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Storage;

class RocketService implements APIAppsInterface
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

    /**
     * @return JsonResponse
     */
    public function serve(): JsonResponse
    {
        $data = collect(['success' => true]);
        $vehicle = Vehicle::where('plate', $this->request->get('vehicle'))->first();

        if ($vehicle) {
            switch ($this->service) {
                case 'save-photo':
                    $type = $this->request->get('type');
                    $side = $this->request->get('side');
                    $img = $this->request->get('img');
                    Storage::disk('local')->append('photo.log', "$img" . Carbon::now() . "\n$side: $type\n$vehicle->plate:\n");
                    break;

                case 'save-battery':
                    $payload = collect($this->request->only([
                        'level',
                        'charging',
                        'dateChanged',
                        'date'
                    ]));

                    Storage::disk('local')->append('battery.log', "$vehicle->plate: ".$payload->toJson());
                    return response()->json($data->toArray());
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => __('Service not found')
                    ]);
                    break;
            }
        }

        return response()->json([
            'success' => false,
            'message' => __('Vehicle not found')
        ]);
    }
}