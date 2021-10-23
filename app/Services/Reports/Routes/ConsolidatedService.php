<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 9/12/2018
 * Time: 12:09 AM
 */

namespace App\Services\Reports\Routes;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Http\Controllers\Utils\Geolocation;
use App\Services\PCWExporterService;
use Excel;

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
    /**
     * @var ControlPointService
     */
    private $controlPointService;

    /**
     * ReportsService constructor.
     * @param OffRoadService $offRoadService
     * @param SpeedingService $speedingService
     * @param ControlPointService $controlPointService
     */
    public function __construct(OffRoadService $offRoadService, SpeedingService $speedingService, ControlPointService $controlPointService)
    {
        $this->offRoadService = $offRoadService;
        $this->speedingService = $speedingService;
        $this->controlPointService = $controlPointService;
    }

    /**
     * @param Company $company
     * @param $dateReport
     * @return \Illuminate\Support\Collection
     */
    function buildDailyReport(Company $company, $dateReport)
    {

        $consolidatedReports = collect([]);
        $routes = $company->activeRoutes
            ->where('id', '<>', 183); // TODO: Let route 183 when all parameters are configured

        foreach ($routes as $route) {
            $dispatchRegisters = DispatchRegister::active()
                ->where('route_id', $route->id)
                ->where('date', $dateReport)
                ->orderBy('departure_time')
                ->get();

            $reportVehicleByRoute = collect([]);
            foreach ($dispatchRegisters as $dispatchRegister) {
                $vehicle = $dispatchRegister->vehicle;

                $offRoadReport = $this->offRoadService->offRoadsByDispatchRegister($dispatchRegister);
                $speedingReport = $this->speedingService->speedingByDispatchRegister($dispatchRegister);
                $controlPointReport = $this->controlPointService->controlPointReportWithDelay($dispatchRegister);

                $totalOffRoads = $offRoadReport->count();
                $totalSpeeding = $speedingReport->count();
                $controlPointReportTotal = $controlPointReport->count();
                $hasEvent = ($totalOffRoads > 0 || $totalSpeeding > 0 || $controlPointReportTotal > 0);

                if ($hasEvent) {
                    $reportVehicleByRoute->put($dispatchRegister->id, (object)[
                        'vehicle' => $vehicle,
                        'dispatchRegister' => $dispatchRegister,
                        'offRoadReport' => $offRoadReport,
                        'totalOffRoads' => $totalOffRoads,
                        'speedingReport' => $speedingReport,
                        'totalSpeeding' => $totalSpeeding,
                        'controlPointReport' => $controlPointReport,
                        'controlPointReportTotal' => $controlPointReportTotal
                    ]);
                }
            }

            $consolidatedReports->put($route->id, (object)[
                'route' => $route,
                'date' => $dateReport,
                'reportVehicleByRoute' => $reportVehicleByRoute,
                'totalReports' => $reportVehicleByRoute->count()
            ]);
        }

        return $consolidatedReports;
    }

    /**
     * @param $consolidatedReports
     * @return \Illuminate\Support\Collection
     */
    function buildDailyReportFiles($consolidatedReports)
    {
        $pathsToConsolidatesReportFiles = collect([]);
        foreach ($consolidatedReports as $consolidatedReport) {
            $route = $consolidatedReport->route;
            $date = $consolidatedReport->date;

            if( $consolidatedReport->totalReports ){
                $reportVehicleByRoute = $consolidatedReport->reportVehicleByRoute;

                $fileNameSheet = __('Consolidated') . " $route->name" . " $date";
                $fileName = str_replace([' ', '-'], '_', $fileNameSheet);
                $fileExtension = 'xlsx';


                $excel = Excel::create($fileName, function ($excel) use ($fileNameSheet, $reportVehicleByRoute, $route, $date) {

                    $dataExcel = array();
                    foreach ($reportVehicleByRoute as $reportByVehicle) {
                        $vehicle = $reportByVehicle->vehicle;
                        $dispatchRegister = $reportByVehicle->dispatchRegister;
                        $driver = $dispatchRegister->driver;

                        $details = $this->buildStringDetails($reportByVehicle);

                        $link = route('link-report-route-chart-view', ['dispatchRegister' => $dispatchRegister->id, 'location' => 0]);


                        $dataExcel[] = [
                            __('Turn') => $dispatchRegister->turn,                                                     # A CELL
                            __('Round Trip') => $dispatchRegister->round_trip,                                         # B CELL
                            __('Vehicle') => $vehicle->number,                                                         # C CELL
                            __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                   # D CELL
                            __('Off Roads') => $reportByVehicle->totalOffRoads,                                        # E CELL
                            __('Off roads details') => "$details->offRoadReportString",                                # F CELL
                            __('Speeding') => $reportByVehicle->totalSpeeding,                                         # G CELL
                            __('Speeding details') => $details->speedingReportString,                                  # H CELL
                            __('Delay control points') => $reportByVehicle->controlPointReportTotal,                   # I CELL
                            __('Control points details') => $details->delayControlPointsReportString,                  # J CELL
                            __('Details') => $link,                                                                    # K CELLs
                        ];
                    }

                    if($dataExcel){
                        $dataExport = (object)[
                            'fileName' => $fileNameSheet,
                            'title' => __('Consolidated') . " $route->name",
                            'subTitle' => "$date",
                            'sheetTitle' => "$route->name",
                            'data' => $dataExcel,
                            'type' => 'consolidatedRouteReport'
                        ];

                        /* SHEETS */
                        $excel = PCWExporterService::createHeaders($excel, $dataExport);
                        $excel = PCWExporterService::createSheet($excel, $dataExport);
                    }
                })->store($fileExtension);

                $pathsToConsolidatesReportFiles->push("$excel->storagePath/$fileName.$fileExtension");
            }else{
                dump("No reports found for $route->name on date $date");
            }
        }

        return $pathsToConsolidatesReportFiles;
    }

    /**
     * Build string report details
     *
     * @param $reportByVehicle
     * @return object
     */
    private function buildStringDetails($reportByVehicle)
    {
        $index = 0;
        $offRoadReportString = "";
        foreach ($reportByVehicle->offRoadReport as $offRoadReport) {
            $ln = $index > 0 ? "\n" : "";
            $time = $offRoadReport->date->toTimeString();

            $offRoadReportString .= "$ln • $time → " . Geolocation::getAddressFromCoordinates($offRoadReport->latitude, $offRoadReport->longitude);
            $index++;
        }

        $index = 0;
        $speedingReportString = "";
        foreach ($reportByVehicle->speedingReport as $speedingReport) {
            $ln = $index > 0 ? "\n" : "";
            $time = $speedingReport->time->toTimeString();

            $speedingReportString .= "$ln • $time → $speedingReport->speed Km/h " . Geolocation::getAddressFromCoordinates($speedingReport->latitude, $speedingReport->longitude);
            $index++;
        }

        $index = 0;
        $delayControlPointsReportString = "";
        foreach ($reportByVehicle->controlPointReport as $controlPointReport) {
            $report = $controlPointReport->report;
            $ln = $index > 0 ? "\n" : "";
            $delayControlPointsReportString .= "$ln • " . __('Time') . " $report->measuredControlPointTime $controlPointReport->controlPointName (Ref. $controlPointReport->maxTime) → " . __('Reported at') . " $report->timeMeasured";
            $index++;
        }

        return (object)[
            'offRoadReportString' => $offRoadReportString,
            'speedingReportString' => $speedingReportString,
            'delayControlPointsReportString' => $delayControlPointsReportString
        ];
    }
}