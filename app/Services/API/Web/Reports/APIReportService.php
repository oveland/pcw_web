<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Web\Reports;

use App\Models\Company\Company;
use App\Models\Passengers\CurrentSensorPassengers;
use App\Models\Reports\ConsolidatedRouteVehicle;
use App\Models\Routes\DispatchRegister;
use App\Services\API\Web\Contracts\APIWebInterface;
use App\Services\Reports\Routes\ControlPointService;
use App\Services\Reports\Routes\RouteService;
use App\Services\Reports\Vehicles\ConsolidatedService;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class APIReportService implements APIWebInterface
{
    public $controlPointService;
    /**
     * @var RouteService
     */
    private $routeService;
    /**
     * @var ConsolidatedService
     */
    private $consolidatedVehicle;

    /**
     * APIReportService constructor.
     * @param ControlPointService $controlPointService
     * @param RouteService $routeService
     * @param ConsolidatedService $consolidatedVehicle
     */
    public function __construct(ControlPointService $controlPointService, RouteService $routeService, ConsolidatedService $consolidatedVehicle)
    {
        $this->controlPointService = $controlPointService;
        $this->routeService = $routeService;
        $this->consolidatedVehicle = $consolidatedVehicle;
    }


    /**
     * @param $service
     * @param Request $request
     * @return JsonResponse | string
     */
    public function serve($service, Request $request): JsonResponse
    {
        switch ($service) {
            case 'control-points':
                $dispatchRegister = DispatchRegister::find($request->get('dispatch-register'));

                if( $dispatchRegister ){
                    $response = [
                        'error' => false,
                        'report' => $this->buildControlPointReport($dispatchRegister)
                    ];
                }else{
                    $response = [
                        'error' => true,
                        'message' => 'Dispatch register not found'
                    ];
                }


                return response()->json($response);

                break;

            case 'current-vehicle-status':
                $company = Company::find($request->get('company'));


                if(!$company){
                    $response = [
                        'error' => true,
                        'message' => "Company doesn't exists"
                    ];

                    return response()->json($response);
                }else{
                    $routeReport = 'all';
                    $vehicleReport = 'all';
                    $onlyActive = true;

                    $managementReport = $this->routeService->dispatch->buildCurrentVehicleStatusReport($company, $routeReport, $vehicleReport, $onlyActive);

                    $this->routeService->export->exportCurrentVehicleStatusReport($managementReport);
                }
                break;

            case 'consolidated-route-vehicle':
                $company = Company::find($request->get('company'));
                $fromRequest = $request->get('from');
                $toRequest = $request->get('to');
                if(!$company || !$fromRequest || !$toRequest){
                    return response()->json([
                        'error' => true,
                        'message' => "No params yet"
                    ]);
                }

                $from = Carbon::createFromFormat('Y-m-d', $fromRequest);
                $to = Carbon::createFromFormat('Y-m-d', $toRequest);

                $this->consolidatedVehicle->build($company, $from, $to);

                return response()->json([
                    'error' => false,
                    'message' => 'Process executed successfully'
                ]);
                break;

            case 'export-consolidated-route-vehicle':
                $company = Company::find($request->get('company'));
                $fromRequest = $request->get('from');
                $toRequest = $request->get('to');
                if(!$company || !$fromRequest || !$toRequest){
                    return response()->json([
                        'error' => true,
                        'message' => "No params yet"
                    ]);
                }

                $from = Carbon::createFromFormat('Y-m-d', $fromRequest);
                $to = Carbon::createFromFormat('Y-m-d', $toRequest);

                $this->consolidatedVehicle->export($company, $from, $to);

                return response()->json([
                    'error' => false,
                    'message' => 'Process executed successfully'
                ]);
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
     * Build control point report from dispatch register
     *
     * @param $dispatchRegister
     * @return object
     */
    public function buildControlPointReport(DispatchRegister $dispatchRegister)
    {
        $controlPointReportsByDispatchRegister = $this->controlPointService->buildControlPointReportsByDispatchRegister($dispatchRegister, $dispatchRegister->controlPointTimeReports);

        $report = collect([]);
        $report->put('dispatchRegister', $dispatchRegister->getAPIFields());
        $report->put('reportsByControlPoint', $controlPointReportsByDispatchRegister->reportsByControlPoint);

        return $report;
    }

}