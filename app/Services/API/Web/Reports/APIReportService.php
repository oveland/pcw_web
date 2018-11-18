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
use App\Models\Routes\DispatchRegister;
use App\Services\API\Web\Contracts\APIWebInterface;
use App\Services\Reports\Routes\ControlPointService;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class APIReportService implements APIWebInterface
{
    /**
     * @param $service
     * @param Request $request
     * @return JsonResponse
     */
    public static function serve($service, Request $request): JsonResponse
    {
        switch ($service) {
            case 'control-points':
                $dispatchRegister = DispatchRegister::find($request->get('dispatch-register'));

                if( $dispatchRegister ){
                    $response = [
                        'error' => false,
                        'report' => self::build($dispatchRegister)
                    ];
                }else{
                    $response = [
                        'error' => true,
                        'message' => 'Dispatch register not found'
                    ];
                }


                return response()->json($response);

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
    public static function build(DispatchRegister $dispatchRegister)
    {
        $service = new ControlPointService();
        $controlPointReportsByDispatchRegister = $service->buildControlPointReportsByDispatchRegister($dispatchRegister, $dispatchRegister->controlPointTimeReports);

        $report = collect([]);
        $report->put('dispatchRegister', $dispatchRegister->getAPIFields());
        $report->put('reportsByControlPoint', $controlPointReportsByDispatchRegister->reportsByControlPoint);

        return $report;
    }

}