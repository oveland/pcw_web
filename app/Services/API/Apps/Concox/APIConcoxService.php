<?php

namespace App\Services\API\Apps\Concox;

use App\Models\Apps\Concox\PhotoRequest;
use App\Models\Vehicles\Vehicle;
use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use App\Services\Apps\Concox\ConcoxService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Log;

class APIConcoxService implements APIAppsInterface
{
    /**
     * @var Request | Collection
     */
    private $request;

    /**
     * @var string
     */
    private $service;

    /**
     * @var ConcoxService
     */
    private $concox;

    /**
     * APIConcoxService constructor.
     * @param $service
     */
    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');
        $this->concox = new ConcoxService();
    }

    /**
     * @return JsonResponse
     */
    public function serve(): JsonResponse
    {
        switch ($this->service) {
            case 'take-photo':
//                $vehicle = Vehicle::find(1207); // Vehicle 322 Alameda
                //$vehicle = Vehicle::find(1217); // Vehicle 375 Alameda
                $vehicle = Vehicle::find(1199); // Vehicle 566 YB
//                $vehicle = Vehicle::find(2271); // Vehicle TS1 TS

// $vehicle = Vehicle::find(2136); // Vehicle 70 YB

                $lastPhotoRequest = PhotoRequest::where('vehicle_id', $vehicle->id)->first();

                $now = Carbon::now();
                if (!$lastPhotoRequest || $now->diffInSeconds($lastPhotoRequest->date) >= 30) {

                    if (!$lastPhotoRequest) {
                        $lastPhotoRequest = new PhotoRequest();
                    }

                    $lastPhotoRequest->date = $now->toDateTimeString();
                    $lastPhotoRequest->vehicle()->associate($vehicle);
                    $lastPhotoRequest->type = 'front';
                    $lastPhotoRequest->params = explode('?', $this->request->getRequestUri())[1] ?? '';
                    $lastPhotoRequest->save();

                    $camera = $this->request->get('camera');

                    $response = $this->concox->takePhoto($camera);
                    Log::info("Take photo for vehicle ".$vehicle->number);
                    sleep(30);
                    $this->concox->syncPhotos($camera, 60, 30);
                    Log::info("Sync photos for vehicle ".$vehicle->number);


                } else {
                    $response = collect([
                        'success' => false,
                        'message' => 'Out of limit request'
                    ]);
                }

                return response()->json($response->toArray());
            case 'get-photo':
                $camera = $this->request->get('camera');
                $minutesAgo = intval($this->request->get('minutes-ago'));
                $limit = intval($this->request->get('limit'));
                $page = intval($this->request->get('page'));

                $camera = $camera ? $camera : '1';
                $minutesAgo = $minutesAgo ? $minutesAgo : 2;
                $limit = $limit ? $limit : 2;
                $page = $page ? $page : 0;

                $photos = $this->concox->getPhoto($camera, $minutesAgo, $limit, $page);

                dd($photos->pluck('file_URL'));

                return response()->json($photos->toArray());
                break;
            case 'get-live-stream-video':
                $response = $this->concox->getLiveStreamVideo();
                return response()->json($response->toArray());
                break;
            case 'get-commands-support-list':
                $response = $this->concox->getCommandSupportList();
                return response()->json($response->toArray());
                break;
            case 'sync-photos':
                $camera = $this->request->get('camera') ?? '1';
                $minutesAgo = $this->request->get('minutes-ago') ?? 30;
                $limit = $this->request->get('limit') ?? 30;
                $page = $this->request->get('page') ?? 0;

                $response = $this->concox->syncPhotos($camera, $minutesAgo, $limit, $page);

                return response()->json($response->toArray());
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => __('Service not found')
                ]);
                break;
        }
    }
}
