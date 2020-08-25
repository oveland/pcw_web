<?php

namespace App\Services\API\Apps\Concox;

use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\API\Apps\Contracts\APIFilesInterface;
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
     * @var APIAuthConcoxService
     */
    private $auth;

    /**
     * APIConcoxService constructor.
     * @param $service
     */
    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');

        $this->auth = new APIAuthConcoxService();
    }

    /**
     * @return JsonResponse
     */
    public function serve(): JsonResponse
    {
        switch ($this->service) {
            case 'get-access-token':
                return $this->getAccessToken();
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => __('Service not found')
                ]);
                break;
        }
    }

    /**
     * @return JsonResponse
     */
    public function getAccessToken()
    {
        $access = $this->auth->getAccessToken();

        dd($access);

        return response()->json($accessToken->toArray());
    }
}
