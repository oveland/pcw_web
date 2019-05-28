<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 8/12/2018
 * Time: 10:59 PM
 */

namespace App\Services\Reports\Passengers;


use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;

use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;

use App\Services\PCWExporterService;
use Carbon\Carbon;

class ConsolidatedService
{
    /**
     * Build passenger report from company and date
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