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
use Illuminate\Support\Facades\DB;

class PCWPassengersService implements APIWebInterface
{
    /**
     * @param $service
     * @param Request $request
     * @return JsonResponse
     */
    public function serve($service, Request $request): JsonResponse
    {
        switch ($service) {
            case 'report':
                $company = Company::find($request->get('company'));
                $dateReport = $request->get('date');
                $dateReport = $dateReport ? $dateReport : Carbon::now()->toDateString();

                return response()->json([
                    'error' => false,
                    'passengersReport' => $this->buildPassengersReport($company, $dateReport)
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
    public function buildPassengersReport(Company $company, $dateReport)
    {
        $allDispatchRegisters = DispatchRegister::whereCompanyAndDateAndRouteIdAndVehicleId($company, $dateReport, 'all', 'all')
            ->active()
            ->with('vehicle')
            ->with('route')
            ->orderBy('id')
            ->get();

        $passengersReport = CounterBySensor::report($allDispatchRegisters);

        $reports = array();
        foreach ($passengersReport->report as $vehicleId => $sensor) {
            $vehicle = $sensor->vehicle;

            if($vehicle->number == '889') {
                $sensor->passengersAllBySensor = 1000;
            }

            $countHistory = $this->getRecorderHistory($sensor->history);
            $currentSensor = CurrentSensorPassengers::whereVehicle($vehicle);

            $currentCharges = collect(DB::select("SELECT id, tariff, charge, total_counted, (tariff * total_counted) total_charge FROM current_tariff_charges WHERE vehicle_id = $vehicle->id"));

//            $currentCharges2 = collect(DB::select("
//                SELECT tariff, sum(counted) \"totalCounted\", tariff * sum(counted) \"totalCharge\"
//                FROM passengers
//                WHERE vehicle_id = 1199 and date >= current_Date and dispatch_register_id is not null and tariff > 0
//                GROUP BY tariff
//                ORDER BY tariff
//            "));

            if($vehicleId == 1199 && request()->get('d')) {
//                dd('Ehesss', $currentCharges, $currentCharges2);
            }

            $reports[] = (object)[
                'vehicle_id' => $vehicleId,
                'passengers' => (object)[
                    'recorder' => $sensor->passengersByRecorder,
                    'recorderHistory' => $countHistory,
                    'sensor' => $sensor->passengersBySensor,
                    'sensorAll' => $sensor->passengersAllBySensor,
                    'currentCharges' => $currentCharges,
                    'sensorRecorder' => $sensor->passengersBySensorRecorder,
                    'timeRecorder' => $sensor->timeRecorder,
                    'timeSensor' => $currentSensor->timeSensor,
                    'dateSensor' => $currentSensor->date,
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

    /**
     * @param $history
     * @return Collection
     */
    function getRecorderHistory($history){
        $recorderHistory = collect([]);
        foreach ($history as $item){
            $recorderHistory->push([
                'departureTime' => $item->departureTime,
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
}
