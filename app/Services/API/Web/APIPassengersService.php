<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Web;

use App\Models\Company\Company;
use App\Models\Passengers\CurrentSensorPassengers;
use App\Models\Routes\DispatchRegister;
use App\Services\API\Web\Contracts\APIWebInterface;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class APIPassengersService implements APIWebInterface
{
    /**
     * @var Request
     */
    private $request;
    private $service;

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
            case 'report':
                return $this->buildPassengersReport();
                break;
            case 'sync':
                return $this->syncPassengers();
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
    public function buildPassengersReport()
    {
        $company = Company::find($this->request->get('company'));
        $dateReport = $this->request->get('date');
        $dateReport = $dateReport ? $dateReport : Carbon::now()->toDateString();

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
            $recorderHistory = $recorder ? $this->getRecorderHistory($recorder->history) : [];

            $currentSensor = CurrentSensorPassengers::whereVehicle($vehicle);
            $reports[] = (object)[
                'vehicle_id' => $vehicleId,
                'passengers' => (object)[
                    'recorder' => $recorder ? $recorder->passengersByRecorder : 0,
                    'recorderHistory' => $recorderHistory,
                    'sensor' => $sensor->passengersBySensor,
                    'sensorRecorder' => $sensor->passengersBySensorRecorder,
                    'timeRecorder' => $recorder->timeRecorder,
                    'timeSensor' => $currentSensor->timeSensor,
                    'dateSensor' => $currentSensor->date,
                    'timeSensorRecorder' => $currentSensor->timeSensorRecorder,
                ]
            ];
        }

        return response()->json([
            'error' => false,
            'passengersReport' => (object)[
                'date' => $dateReport,
                'companyId' => $company->id,
                'reports' => $reports
            ]
        ]);
    }

    /**
     * @param $history
     * @return Collection
     */
    private function getRecorderHistory($history)
    {
        $recorderHistory = collect([]);
        foreach ($history as $item) {
            $recorderHistory->push([
                'routeId' => $item->routeId,
                'routeName' => $item->routeName,
                'roundTrip' => $item->roundTrip,
                'turn' => $item->turn,
                'passengersByRoundTrip' => $item->passengersByRoundTrip,
                'startRecorder' => $item->startRecorder,
                'endRecorder' => $item->endRecorder,
                'driverCode' => $item->dispatchRegister ? $item->dispatchRegister->driver_code : '',
            ]);
        }

        return $recorderHistory;
    }

    /**
     * @return JsonResponse
     */
    private function syncPassengers() {
        Artisan::call('lm:sync', [
            '--company' => $this->request->get('company'),
            '--date' => $this->request->get('date'),
        ]);

        return response()->json([
            'error' => false,
            'message'=> 'Sync successfully'
        ]);
    }
}