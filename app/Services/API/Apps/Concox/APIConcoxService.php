<?php

namespace App\Services\API\Apps\Concox;

use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use App\Services\Apps\Concox\ConcoxService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
                $camera = $this->request->get('camera');

                $response = $this->concox->takePhoto($camera);
                sleep(30);
                $this->concox->syncPhotos($camera, 60, 30);

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
                $camera = $this->request->get('camera');
                $minutesAgo = $this->request->get('minutes-ago');
                $limit = $this->request->get('limit');

                $response = $this->concox->syncPhotos($camera, $minutesAgo, $limit);

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
