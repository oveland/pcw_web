<?php

namespace App\Services\API\Apps\Concox;

use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use App\Services\Apps\Concox\ConcoxService;
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
                return response()->json($response->toArray());
            case 'get-photo':
                $camera = $this->request->get('camera');

                $photos = $this->concox->getPhoto($camera);
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
            default:
                return response()->json([
                    'success' => false,
                    'message' => __('Service not found')
                ]);
                break;
        }
    }
}
