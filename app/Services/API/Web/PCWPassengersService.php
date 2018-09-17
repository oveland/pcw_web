<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Web;

use App\Company;
use App\CurrentSensorPassengers;
use App\DispatchRegister;
use App\Services\API\Web\Contracts\APIWebInterface;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PCWPassengersService implements APIWebInterface
{
    /**
     * @param $service
     * @param Request $request
     * @return JsonResponse
     */
    public static function serve($service, Request $request): JsonResponse
    {
        switch ($service) {
            case 'report':
                $company = Company::find($request->get('company'));
                $dateReport = Carbon::now()->toDateString();

                return response()->json([
                    'error' => false,
                    'passengersReport' => self::buildPassengersReport($company, $dateReport)
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
     * Build passenger report from company and date
     *
     * @param $company
     * @param $dateReport
     * @return object
     */
    public static function buildPassengersReport(Company $company, $dateReport)
    {
        $routes = $company->routes;
        $allDispatchRegisters = DispatchRegister::active()
            ->whereIn('route_id', $routes->pluck('id'))
            ->where('date', $dateReport)
            ->with('vehicle')
            ->with('route')
            ->orderBy('id')
            ->get();

        $passengerBySensor = CounterBySensor::report($allDispatchRegisters);
        $passengerByRecorder = CounterByRecorder::report($allDispatchRegisters);

        $reports = array();
        foreach ($passengerBySensor->report as $vehicleId => $sensor) {
            $vehicle = Vehicle::find($vehicleId);

            $recorder = isset($passengerByRecorder->report["$vehicleId"]) ? $passengerByRecorder->report["$vehicleId"] : null;
            $currentSensor = CurrentSensorPassengers::whereVehicle($vehicle);

            $reports[] = (object)[
                'vehicle_id' => $vehicleId,
                'passengers' => (object)[
                    'recorder' => $recorder ? $recorder->passengersByRecorder : 0,
                    'sensor' => $sensor->passengersBySensor,
                    'sensorRecorder' => $sensor->passengersBySensorRecorder,
                    'timeRecorder' => $recorder->timeRecorder,
                    'timeSensor' => $currentSensor->timeSensor,
                    'timeSensorRecorder' => $currentSensor->timeSensorRecorder,
                ]
            ];
        }

        $passengerReport = (object)[
            'date' => $dateReport,
            'companyId' => $company->id,
            'reports' => $reports
        ];

        return $passengerReport;
    }

}