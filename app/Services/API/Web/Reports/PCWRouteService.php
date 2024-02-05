<?php

namespace App\Services\API\Web\Reports;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\DrObservation;
use App\Services\API\Web\Contracts\APIWebInterface;
use App\Traits\CounterBySensor;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

    function getDateRange($dateReport, $spreadsheetReport): object
    {
        $now = Carbon::now()->toDateString();
        $initial = $dateReport ?: $now;
        $final = $initial;

        if ($spreadsheetReport) {
            $drObservations = DrObservation::where('observation', $spreadsheetReport)
                ->with('dispatchRegister')
                ->get();

            $initial = $drObservations->min('dispatchRegister.date') ?: $initial;
            $final = $drObservations->max('dispatchRegister.date') ?: $final;
        }

        return (object)[
            'initial' => $initial,
            'final' => $final
        ];
    }

    /**
     * @param $service
     * @param Request $request
     * @return JsonResponse
     */
    function serve($service, Request $request): JsonResponse
    {
        switch ($service) {
            case 'report-dispatch':
                $report = collect([]);

                $dateReportRequest = $request->get('date');

                $company = $this->getCompany($request->get('company'));
                if ($company) {
                    $routeReport = $request->get('route');
                    $vehicleReport = $request->get('vehicle');
                    $plateVehicle = $request->get('plate');
                    $spreadsheetReport = $request->get('spreadsheet');

                    $dateRange = $this->getDateRange($dateReportRequest, $spreadsheetReport);

                    $dateInitialReport = $dateRange->initial;
                    $dateEndReport = $dateRange->final;

                    $vehicle = null;
                    $vehiclePlate = null;

                    if (is_string($plateVehicle)) $vehiclePlate = Vehicle::where('plate', $plateVehicle)->first();
                    if (is_numeric($vehicleReport)) $vehicle = Vehicle::where('number', $vehicleReport)->first();

                    if ($vehiclePlate && $vehiclePlate->belongsToCompany($company)) {
                        $vehicleId = $vehiclePlate->id;
                    } else if ($vehicle && $vehicle->belongsToCompany($company)) {
                        $vehicleId = $vehicle->id;
                    } else {
                        $vehicleId = null;
                    }

                    if ($vehicle || !$vehicleReport) $report = $this->buildPassengersReport($company, $dateReportRequest, $dateInitialReport, $dateEndReport, $routeReport, $vehicleId, $spreadsheetReport);
                }

                return response()->json([
                    'error' => false,
                    'data' => (object)[
                        'date' => $dateReportRequest,
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

    public function buildPassengersReport(Company $company, $dateReportRequest, $dateInitialReport, $dateEndReport, $routeReport, $vehicleReport, $spreadsheetReport): Collection
    {
        $allDispatchRegisters = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($company, $dateInitialReport, $dateEndReport, $routeReport, $vehicleReport)
            ->active()
            ->with('vehicle')
            ->with('route')
            ->orderBy('id')
            ->get();

        if ($dateReportRequest) {
            $allDispatchRegisters = $allDispatchRegisters->filter(function (DispatchRegister $d) use ($dateReportRequest) {
                return $d->date == $dateReportRequest;
            });
        }

        if ($spreadsheetReport) {
            $allDispatchRegisters = $allDispatchRegisters->filter(function (DispatchRegister $d) use ($spreadsheetReport) {
                $observation1 = $d->getObservation('spreadsheet_passengers')->observation;

                if (($observation1 ?? '') == $spreadsheetReport) {
                    return true;
                } else {
                    $observation2 = $d->getObservation('spreadsheet_passengers_sync')->observation;
                    if (($observation2 ?? '') == $spreadsheetReport) {
                        return true;
                    } else {
                        return false;
                    }
                }
            });
        }

        $passengersReport = CounterBySensor::report($allDispatchRegisters)->report;
        return $allDispatchRegisters->map(function (DispatchRegister $d) use ($passengersReport) {
            $passengersVehicle = $passengersReport->get($d->vehicle->id);
            $passengersRoundTrip = $passengersVehicle->history->get($d->id);
            $totalPassengers = $passengersRoundTrip->totalByRecorderByRoundTrip;

            if (!($d->route_id == 279 || $d->route_id == 280 || $d->route_id == 281 || $d->route_id == 283)) {
                $topologies = \App\Models\Vehicles\TopologiesSeats::query() //total asientos de VH
                ->where('vehicle_id', $d->vehicle->id)
                    ->with('vehicle')
                    ->get();

                $totalSeats = 0;
                $totalPassengers = 0;
                $totalPassengersAE = 0;

                foreach ($topologies as $topology) {
                    $numSeatsCam = $topology->number_seats;
                    if (is_numeric($numSeatsCam)) {
                        $totalSeats += $numSeatsCam;
                    }
                }
                $spreadsheetPassengersSync = $d->getObservation('spreadsheet_passengers_sync');
                $countMax = $d->final_front_sensor_counter;
                $countMaxAssets = $countMax >= $totalSeats ? $totalSeats : $countMax;
                $countPassengersFICS = $spreadsheetPassengersSync->value;
                $countBySensorFinal = $d->final_sensor_counter;

                $countLongRoute = $countPassengersFICS >= $countBySensorFinal ? $countPassengersFICS : $countBySensorFinal;

                $totalPassengersmax = $countMaxAssets >= $spreadsheetPassengersSync->value ? $countMaxAssets : $countPassengersFICS;

                if ($d->route_id == 337 || $d->route_id == 338 ){
                    $totalPassengers = $countLongRoute ? $countLongRoute : 0;
                }else {
                    $totalPassengers = $totalPassengersmax;
                }
            }

            // TODO: Cambiar cuando se haga recaudo:
            $tariffPassenger = $d->route->tariff->passenger;
            $totalProduction = $tariffPassenger * $totalPassengers;

            return [
                'vehicle' => [
                    'id' => $d->vehicle->id,
                    'number' => $d->vehicle->number,
                    'plate' => $d->vehicle->plate

                ],
                'route' => [
                    'id' => $d->route->id,
                    'name' => $d->route->name
                ],
                'passengers' => [
                    'spreadsheet' => $d->getObservation('end_recorder')->observation,
                    'tariff' => $tariffPassenger,
                    'total' => $totalPassengers,
                    'totalProduction' => $totalProduction,
                    //'totalSensor' => $passengersRoundTrip->totalBySensorByRoundTrip,
                    //'totalProductionSensor' => $totalProductionSensor,
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
        })->values();
    }
}
