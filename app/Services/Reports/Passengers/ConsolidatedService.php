<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 8/12/2018
 * Time: 10:59 PM
 */

namespace App\Services\Reports\Passengers;


use App\Exports\Passengers\ConsolidatedDailyExport;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;

use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;

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
                'vehicle' => $sensor->vehicle->getAPIFields(),
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
        $file = new ConsolidatedDailyExport($passengerReports);

        if ($download) return $file->download();


        $path = "exports/passengers/$file->fileName";
        $file->store($path);

        return $path;
    }
}