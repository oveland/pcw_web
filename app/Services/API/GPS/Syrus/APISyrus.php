<?php


namespace App\Services\API\GPS\Syrus;


use App\Services\API\GPS\Contracts\APIGPSInterface;
use App\Services\GPS\Syrus\SyrusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class APISyrus implements APIGPSInterface
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
     * @var SyrusService
     */
    private $syrus;

    /**
     * APIConcoxService constructor.
     * @param $service
     */
    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');
        $this->syrus = new SyrusService();
    }

    public function serve(): JsonResponse
    {
        switch ($this->service) {
            case 'sync-photo':
                $response = $this->syrus->syncPhoto($this->request->get('imei'));
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