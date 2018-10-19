<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 10/10/2018
 * Time: 9:47 PM
 */

namespace App\Services\pcwserviciosgps;

use App\Company;
use App\DispatchRegister;
use App\Http\Controllers\Utils\Geolocation;
use App\Services\PCWExporter;
use App\Services\pcwserviciosgps\reports\routes\ControlPointService;
use App\Services\pcwserviciosgps\reports\routes\OffRoadService;
use App\Services\pcwserviciosgps\reports\routes\SpeedingService;
use Carbon\Carbon;
use Excel;

class ConsolidatedReportsService
{
    private $offRoadService;
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
    function generateConsolidatedReportDaily(Company $company, $dateReport)
    {

        $consolidatedReports = collect([]);
        $routes = $company->routes;

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
                    $reportVehicleByRoute->put($dispatchRegister->turn, (object)[
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
    function generateConsolidatedReportFiles($consolidatedReports)
    {
        $pathsToConsolidatesReportFiles = collect([]);
        foreach ($consolidatedReports as $consolidatedReport) {
            $route = $consolidatedReport->route;
            $date = $consolidatedReport->date;
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

                    $dataExcel[] = [
                        __('Turn') => $dispatchRegister->turn,                                                     # A CELL
                        __('Turn') => $dispatchRegister->turn,                                                     # B CELL
                        __('Round Trip') => $dispatchRegister->round_trip,                                         # C CELL
                        __('Vehicle') => intval($vehicle->number),                                                 # D CELL
                        __('Driver') => $driver?$driver->fullName():__('Not assigned'),                       # E CELL
                        __('Off Roads') => $reportByVehicle->totalOffRoads,                                        # F CELL
                        __('Off roads details') => $details->offRoadReportString,                                  # G CELL
                        __('Speeding') => $reportByVehicle->totalSpeeding,                                         # H CELL
                        __('Speeding details') => $details->speedingReportString,                                  # I CELL
                        __('Delay control points') => $reportByVehicle->controlPointReportTotal,                   # J CELL
                        __('Control points details') => $details->delayControlPointsReportString,                  # K CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => $fileNameSheet,
                    'title' => __('Consolidated') . " $route->name",
                    'subTitle' => "$date",
                    'sheetTitle' => "$route->name",
                    'data' => $dataExcel
                ];

                /* SHEETS */
                $excel = PCWExporter::createHeaders($excel, $dataExport);
                $excel = PCWExporter::createSheet($excel, $dataExport);
            })->store($fileExtension);

            $pathsToConsolidatesReportFiles->push("$excel->storagePath/$fileName.$fileExtension");
        }

        return $pathsToConsolidatesReportFiles;
    }

    /**
     * Build string report details
     *
     * @param $reportByVehicle
     * @return object
     */
    function buildStringDetails($reportByVehicle)
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
            $ln = $index > 0 ? "\n" : "";
            $delayControlPointsReportString .= "$ln • $controlPointReport->controlPointName (Ref. $controlPointReport->maxTime) → " . __('Reported at') . " $controlPointReport->timeReport ";
            $index++;
        }

        return (object)[
            'offRoadReportString' => $offRoadReportString,
            'speedingReportString' => $speedingReportString,
            'delayControlPointsReportString' => $delayControlPointsReportString
        ];
    }
}