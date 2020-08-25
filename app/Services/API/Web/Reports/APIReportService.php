<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Web\Reports;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use App\Services\API\Web\Contracts\APIWebInterface;
use App\Services\Reports\Routes\ControlPointService;
use App\Services\Reports\Routes\DispatchRouteService;
use App\Services\Reports\Routes\DispatchService;
use FontLib\TrueType\Collection;
use Illuminate\Http\JsonResponse;

class APIReportService implements APIWebInterface
{

    /**
     * @var DispatchService
     */
    private $dispatchService;

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
            case 'takings-daily':
                return $this->buildTakingsReport();
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
    private function buildTakingsReport()
    {
        $vehicleRequest = $this->request->get('vehicle');
        $routeRequest = $this->request->get('route');
        $finalDate = $this->request->get('final-date');

        $vehicle = Vehicle::find($vehicleRequest);
        $company = $vehicle ? $vehicle->company : Company::find(Company::ALAMEDA);  // TODO: Need control for all vehicles
        if($this->request->get('dump')){
//            dd($company);
        }
        $this->dispatchService = new DispatchService($company);

        $date = $this->request->get('date');

        if ($date) {
            $report = $this->dispatchService->getTakingsReport($date, $finalDate, $routeRequest, $vehicleRequest);

            return response()->json([
                'error' => false,
                'report' => $report
            ]);
        }
        return response()->json([
            'error' => true,
            'message' => __('Vehicle not found')
        ]);
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