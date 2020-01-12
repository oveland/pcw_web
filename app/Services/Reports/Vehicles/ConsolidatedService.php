<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 11/01/2020
 * Time: 10:59 AM
 */

namespace App\Services\Reports\Vehicles;


use App\Models\Company\Company;
use App\Models\Reports\ConsolidatedRouteVehicle;
use App\Models\Vehicles\Vehicle;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;

use App\Services\PCWTime;
use App\Services\Reports\Routes\OffRoadService;
use App\Services\Reports\Routes\SpeedingService;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;

use App\Services\PCWExporterService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ConsolidatedService
{
    /**
     * @var OffRoadService
     */
    private $offRoadService;
    /**
     * @var SpeedingService
     */
    private $speedingService;

    public function __construct(OffRoadService $offRoadService, SpeedingService $speedingService)
    {
        $this->offRoadService = $offRoadService;
        $this->speedingService = $speedingService;
    }

    public function build(Company $company, Carbon $initialDate, Carbon $finalDate)
    {
        Log::useDailyFiles(storage_path().'/logs/consolidatedVehicle.log',10);
        $dateRange = collect(PCWTime::dateRange($initialDate, $finalDate, false, true))->toArray();

        foreach ($dateRange as $date){
            Log::info("Consolidated on: $date");
            $dispatchRegisters = DispatchRegister::completed()
                ->with(['locations', 'route', 'vehicle'])
                ->whereIn('vehicle_id', $company->vehicles->pluck('id'))
                ->where('date', $date )
                ->get();

            $dispatchRegistersByRoutes = $dispatchRegisters->groupBy('route_id');
            foreach ($dispatchRegistersByRoutes as $routeId => $dispatchRegistersByRoute){
                $route = Route::find($routeId);

                $dispatchRegistersByRouteAndVehicles = $dispatchRegistersByRoute->groupBy('vehicle_id');
                foreach ($dispatchRegistersByRouteAndVehicles as $vehicleId => $turns){
                    $vehicle = Vehicle::find($vehicleId);

                    $allRouteLocations = collect([]);
                    foreach ($turns as $turn){
                        $locations = $turn->locations;
                        foreach ($locations as $location){
                            $allRouteLocations->push($location);
                        }
                    }

                    $offRoadEventLocation = $this->offRoadService->groupByFirstOffRoad($allRouteLocations);

                    $speedingEventLocation = $this->speedingService->groupByFirstSpeedingEvent($allRouteLocations);

                    $consolidated = ConsolidatedRouteVehicle::where('date', $date)
                        ->where('route_id', $route->id)
                        ->where('vehicle_id', $vehicle->id)
                        ->first();
                    if(!$consolidated) $consolidated = new ConsolidatedRouteVehicle();

                    $consolidated->fill([
                        'date' => $date,
                        'total_off_roads' => $offRoadEventLocation->count(),
                        'total_speeding' => $speedingEventLocation->count(),
                        'total_locations' => $allRouteLocations->count(),
                    ]);

                    $consolidated->vehicle()->associate($vehicle);
                    $consolidated->route()->associate($route);

                    $consolidated->save();
                }
            }
        }
    }

    /**
     * Build vehicle report from company and date
     *
     * @param Company $company
     * @param $dateReport
     * @return object
     */
    public function buildDailyReport(Company $company, $dateReport)
    {
        $routes = Route::where('company_id', $company->id)->get();
        $dispatchRegisters = DispatchRegister::active()
            ->whereIn('route_id', $routes->pluck('id'))
            ->where('date', $dateReport)
            ->get()
            ->sortBy('id');

        $passengerBySensor = CounterBySensor::report($dispatchRegisters);
        $passengerByRecorder = CounterByRecorder::report($dispatchRegisters);

        // Build report data
        $reports = collect([]);
        foreach ($passengerBySensor->report as $vehicleId => $sensor) {
            $recorder = $passengerByRecorder->report["$vehicleId"];

            $reports->push((object)[
                'vehicle_id' => $vehicleId,
                'date' => $dateReport,
                'passengers' => (object)[
                    'sensor' => $sensor->passengersBySensor,
                    'sensorRecorder' => $sensor->passengersBySensorRecorder,
                    'recorder' => $recorder->passengersByRecorder,
                    'start_recorder' => $recorder->start_recorder,
                    'issue' => $recorder->issue
                ],
                'historyRoutesByRecorder' => $recorder->history->sortBy('routeName')->groupBy('routeId'),
                'historyRoutesBySensor' => $sensor->history->sortBy('routeName')->groupBy('routeId'),
            ]);
        }

        $passengerReport = (object)[
            'date' => $dateReport,
            'companyId' => $company->id,
            'reports' => $reports,
            'totalReports' => $reports->count(),
            'issues' => $passengerByRecorder->issues,
        ];

        //dd($passengerReport);

        return $passengerReport;
    }

    /**
     * Export and store report to excel format
     * returns tag
     *
     * @param $passengerReports
     * @param bool $download
     * @return string
     */
    function exportDailyReportFiles($passengerReports, $download = true)
    {
        $dateReport = $passengerReports->date;

        $dataExcel = array();
        foreach ($passengerReports->reports as $report) {
            $vehicle = Vehicle::find($report->vehicle_id);
            $sensor = $report->passengers->sensor;
            $recorder = $report->passengers->recorder;
            $sensorRecorder = $report->passengers->sensorRecorder;

            $totalRoundTrips = 0;
            $detailedRoutes = "";
            foreach ($report->historyRoutesByRecorder as $routeId => $historyRecorder) {
                $ln = $totalRoundTrips > 0 ? "\n" : "";
                $lastHistory = $historyRecorder->last();
                $totalRoundTrips += $lastHistory->roundTrip;
                $detailedRoutes .= "$ln$lastHistory->routeName : $lastHistory->roundTrip " . __('round trips');
            }

            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Routes') => $detailedRoutes,                                        # D CELL
                __('Round trips') => $totalRoundTrips,                                  # E CELL
                __('Sensor recorder') => intval($sensorRecorder),                       # F CELL
                __('Recorder') => intval($recorder),                                    # G CELL
                __('Sensor') => intval($sensor),                                        # H CELL
            ];
        }

        $fileData = [
            'fileName' => __('Passengers report') . " $dateReport",
            'title' => __('Passengers')."\n".__('Consolidated per day'),
            'subTitle' => Carbon::createFromFormat('Y-m-d', $passengerReports->date)->format('d-m-Y'),
            'data' => $dataExcel,
            'type' => 'passengerReportTotalFooter'
        ];

        if ($download) PCWExporterService::excel($fileData);
        else return PCWExporterService::store($fileData);
    }
}