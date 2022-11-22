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
use App\Models\Users\User;
use App\Services\API\Web\Contracts\APIWebInterface;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PCWRouteService implements APIWebInterface
{
    function getCompany($key)
    {
        switch ($key) {
            case 'ep':
                return Company::find(Company::EXPRESO_PALMIRA);
                break;
            default:
                return null;
        }
    }

    /**
     * @param $service
     * @param Request $request
     * @return JsonResponse
     */
    public function serve($service, Request $request): JsonResponse
    {
        switch ($service) {
            case 'report-dispatch':
                $report = collect([]);

                $dateReport = $request->get('date');
                $dateReport = $dateReport ?: Carbon::now()->toDateString();

                $company = $this->getCompany($request->get('company'));
                if ($company) {
                    $routeReport = $request->get('route');
                    $vehicleReport = $request->get('vehicle');

                    $vehicle = null;
                    if (is_string($vehicleReport)) $vehicle = Vehicle::where('plate', $vehicleReport)->first();
                    if (is_numeric($vehicleReport)) $vehicle = Vehicle::find($vehicleReport);
                    $vehicleId = $vehicle && $vehicle->belongsToCompany($company) ? $vehicle->id : null;

                    if ($vehicle || !$vehicleReport) $report = $this->buildPassengersReport($company, $dateReport, $routeReport, $vehicleId);
                }

                return response()->json([
                    'error' => false,
                    'data' => (object)[
                        'date' => $dateReport,
                        'company' => $company ? $company->name : null,
                        'reports' => $report
                    ]
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

    public function buildPassengersReport(Company $company, $dateReport, $routeReport, $vehicleReport): Collection
    {
        $allDispatchRegisters = DispatchRegister::whereCompanyAndDateAndRouteIdAndVehicleId($company, $dateReport, $routeReport, $vehicleReport)
            ->active()
            ->with('vehicle')
            ->with('route')
            ->orderBy('id')
            ->get();

        $passengersReport = CounterBySensor::report($allDispatchRegisters)->report;

        $report = $allDispatchRegisters->map(function (DispatchRegister $d) use ($passengersReport) {
            $passengersVehicle = $passengersReport->get($d->vehicle->id);
            $passengersRoundTrip = $passengersVehicle->history->get($d->id);

            return [
                'vehicle' => [
                    'id' => $d->vehicle->id,
                    'plate' => $d->vehicle->plate
                ],
                'route' => [
                    'id' => $d->route->id,
                    'name' => $d->route->name
                ],
                'passengers' => [
                    'manual' => $passengersRoundTrip->totalByRecorderByRoundTrip,
                    'sensor' => $passengersRoundTrip->totalBySensorByRoundTrip,
                    'spreadsheet' => $d->getObservation('end_recorder')->observation
                ],
                'dispatchRegister' => [
                    'id' => $d->id,
                    'date' => $d->getParsedDate()->toDateString(),
                    'time' => $d->time,
                    'turn' => $d->turn,
                    'roundTrip' => $d->round_trip,
                    'departureTime' => $d->onlyControlTakings() ? $d->time : $d->departure_time,
                    'arrivalTime' => $d->onlyControlTakings() ? '' : ($d->complete() ? $d->arrival_time : '--:--:--'),
                    'arrivalTimeScheduled' => $d->arrival_time_scheduled,
                    'differenceTime' => $d->arrival_time_difference,
                    'routeTime' => $d->getRouteTime(),
                    'mileage' => $d->getRouteDistance()
                ]
            ];
        });

        return $report;
    }
}
