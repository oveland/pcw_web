<?php

namespace App\Services\API\Files\Operation;

use App\Models\Company\Company;
use App\Services\API\Files\Contracts\APIFilesInterface;
use App\Services\Operation\Routes\BearingService;
use App\Services\Reports\Routes\DispatchRouteService;

class APIBearingService implements APIFilesInterface
{

    /**
     * @var BearingService
     */
    private $bearingService;

    /**
     * APIReportService constructor.
     * @param $service
     */
    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');
    }

    public function serve()
    {
        switch ($this->service) {
            case 'export':
                return $this->export();
                break;
            default:
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid action serve'
                ]);
                break;
        }
    }

    function export()
    {
        $bearingService = new BearingService();
        $data = json_decode($this->request->get('data'), true);

        return $bearingService->export($data);
    }
}