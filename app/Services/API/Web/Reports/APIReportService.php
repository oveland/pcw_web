<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Web\Reports;

use App\Models\Routes\DispatchRegister;
use App\Services\API\Web\Contracts\APIWebInterface;
use App\Services\Reports\Routes\ControlPointService;
use Illuminate\Http\JsonResponse;

class APIReportService implements APIWebInterface
{
    /**
     * APIReportService constructor.
     * @param $service
     */
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
        switch ($this->service) {
            case 'control-points':
                return $this->buildControlPointReport();
                break;
            default:
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid action serve'
                ]);
                break;
        }

    }

    /**
     * @return JsonResponse
     */
    public function buildControlPointReport()
    {
        $controlPointService = new ControlPointService();
        $dispatchRegister = DispatchRegister::find($this->request->get('dispatch-register'));
        $controlPointReportsByDispatchRegister = $controlPointService->buildControlPointReportsByDispatchRegister($dispatchRegister, $dispatchRegister->controlPointTimeReports);

        $report = collect([]);
        $report->put('dispatchRegister', $dispatchRegister->getAPIFields());
        $report->put('reportsByControlPoint', $controlPointReportsByDispatchRegister->reportsByControlPoint);

        if ($dispatchRegister) {
            $response = [
                'error' => false,
                'report' => $report
            ];
        } else {
            $response = [
                'error' => true,
                'message' => 'Dispatch register not found'
            ];
        }

        return response()->json($response);
    }
}