<?php


namespace App\Services\Exports\Routes;


use App\Http\Controllers\Utils\Geolocation;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use App\Services\PCWExporterService;
use App\Traits\CounterByRecorder;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Collection;

class RouteExportService
{
    /**
     * Export excel by Vehicle option
     *
     * @param $vehiclesDispatchRegisters
     * @param $dateReport
     * @internal param $roundTripDispatchRegisters
     */
    public function groupedRouteReport($vehiclesDispatchRegisters, $dateReport)
    {
        Excel::create(__('Dispatch report') . " B " . " $dateReport", function ($excel) use ($vehiclesDispatchRegisters, $dateReport) {
            foreach ($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters) {
                $vehicle = Vehicle::find($vehicleId);
                $vehicleCounter = CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegisters);
                $dataExcel = array();
                $lastArrivalTime = null;

                $totalDeadTime = '00:00:00';

                foreach ($dispatchRegisters as $dispatchRegister) {
                    $historyCounter = $vehicleCounter->report->history[$dispatchRegister->id];
                    $route = $dispatchRegister->route;
                    $driver = $dispatchRegister->driver;

                    $endRecorder = $historyCounter->endRecorder;
                    $startRecorder = $historyCounter->startRecorder;
                    $totalRoundTrip = $historyCounter->passengersByRoundTrip;
                    $totalPassengersByRoute = $historyCounter->totalPassengersByRoute;

                    $deadTime = $lastArrivalTime ? StrTime::subStrTime($dispatchRegister->departure_time, $lastArrivalTime) : '';

                    $dataExcel[] = [
                        __('Route') => $route->name,                                                                    # A CELL
                        __('Round Trip') => $dispatchRegister->round_trip,                                              # B CELL
                        __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                        # C CELL
                        __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # D CELL
                        __('Arrival Time Scheduled') => StrTime::toString($dispatchRegister->arrival_time_scheduled),   # E CELL
                        __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # F CELL
                        __('Arrival Time Difference') => StrTime::toString($dispatchRegister->arrival_time_difference), # G CELL
                        __('Route Time') =>
                            $dispatchRegister->complete() ?
                                StrTime::subStrTime($dispatchRegister->arrival_time, $dispatchRegister->departure_time) :
                                '',                                                                                         # H CELL
                        __('Status') => $dispatchRegister->status,                                                     # I CELL
                        __('Start Rec.') => intval($startRecorder),                                                    # J CELL
                        __('End Rec.') => intval($endRecorder),                                                        # K CELL
                        __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),                          # L CELL
                        __('Pass.') . " " . __('Day') => intval($totalPassengersByRoute),                         # M CELL
                        __('Vehicles without route') => intval($dispatchRegister->available_vehicles),                 # N CELL
                        __('Dead time') => $deadTime,                 # O CELL
                    ];

                    $totalDeadTime = $deadTime ? StrTime::addStrTime($totalDeadTime, $deadTime) : $totalDeadTime;

                    $lastArrivalTime = $dispatchRegister->arrival_time;
                }

                $dataExport = (object)[
                    'fileName' => __('Dispatch report') . " V $dateReport",
                    'title' => __('Dispatch report') . " | $dateReport",
                    'subTitle' => "$vehicle->number | $vehicle->plate" . ". " . __('Total dead time') . ": $totalDeadTime",
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'routeReportByVehicle'
                ];
                /* SHEETS */
                $excel = PCWExporterService::createHeaders($excel, $dataExport);
                $excel = PCWExporterService::createSheet($excel, $dataExport);
            }
        })->export('xlsx');
    }

    /**
     * Export excel by Vehicle option
     *
     * @param $vehiclesDispatchRegisters
     * @param $dateReport
     * @param bool $store
     * @return string
     * @internal param $roundTripDispatchRegisters
     */
    public function ungroupedRouteReport($vehiclesDispatchRegisters, $dateReport, $store = false)
    {
        $fileName = str_replace([' ', '-'], '_', __('Dispatch report') . " UG " . " $dateReport");
        $fileExtension = 'xlsx';
        $excelFile = Excel::create($fileName, function ($excel) use ($vehiclesDispatchRegisters, $dateReport) {
            $dataExcel = collect([]);

            foreach ($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters) {
                $dispatchRegisters = $dispatchRegisters->sortBy('departure_time');

                $vehicle = Vehicle::find($vehicleId);
                $company = $vehicle->company;
                $vehicleCounter = CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegisters);
                $lastArrivalTime = null;

                $totalDeadTime = '00:00:00';

                foreach ($dispatchRegisters as $dispatchRegister) {
                    $historyCounter = $vehicleCounter->report->history[$dispatchRegister->id];
                    $route = $dispatchRegister->route;
                    $driver = $dispatchRegister->driver;

                    $endRecorder = $historyCounter->endRecorder;
                    $startRecorder = $historyCounter->startRecorder;
                    $totalRoundTrip = $historyCounter->passengersByRoundTrip;
                    $totalPassengersByRoute = $historyCounter->totalPassengersByRoute;

                    $deadTime = $lastArrivalTime ? StrTime::subStrTime($dispatchRegister->departure_time, $lastArrivalTime) : '';

                    $data = collect([
                        __('Vehicle') => $vehicle->number,                                                                    # A CELL
                        __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # E CELL
                        __('Arrival Time Scheduled') => StrTime::toString($dispatchRegister->arrival_time_scheduled),   # F CELL
                        __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # G CELL
                        __('Arrival Time Difference') => StrTime::toString($dispatchRegister->arrival_time_difference), # H CELL
                        __('Route Time') =>
                            $dispatchRegister->complete() ?
                                StrTime::subStrTime($dispatchRegister->arrival_time, $dispatchRegister->departure_time) :
                                '',                                                                                         # I CELL
                        __('Route') => $route->name,                                                                    # A CELL
                        __('Round Trip') => $dispatchRegister->round_trip,                                              # B CELL
                        __('Status') => $dispatchRegister->status,                                                     # J CELL
                        __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                        # D CELL
                    ]);

                    if ($company->hasRecorderCounter()) {
                        $data->put(__('Start Rec.'), intval($startRecorder));
                        $data->put(__('End Rec.'), intval($endRecorder));
                        $data->put(__('Pass.') . " " . __('Round Trip'), intval($totalRoundTrip));
                        $data->put(__('Pass.') . " " . __('Day'), intval($totalPassengersByRoute));
                        $data->put(__('Vehicles without route'), intval($dispatchRegister->available_vehicles));
                        $data->put(__('Dead time'), $deadTime);
                    }

                    $dataExcel->push($data->toArray());

                    $totalDeadTime = $deadTime ? StrTime::addStrTime($totalDeadTime, $deadTime) : $totalDeadTime;

                    $lastArrivalTime = $dispatchRegister->arrival_time;
                }

                $data = collect([
                    __('Vehicle') => '----------',
                    __('Departure time') => '----------',
                    __('Arrival Time Scheduled') => '----------',
                    __('Arrival Time') => '----------',
                    __('Arrival Time Difference') => '----------',
                    __('Route Time') => '----------',
                    __('Route') => strtoupper(__('Total round trips')),
                    __('Dead time') => number_format($dispatchRegisters->count() / 2, '1', '.', '')
                ]);

                $dataExcel->push($data->toArray());
            }

            $dataExport = (object)[
                'fileName' => __('Dispatch report') . " $dateReport",
                'title' => __('Dispatch report') . " | $dateReport",
                'subTitle' => __('Total vehicles') . ": " . $vehiclesDispatchRegisters->count(),
                'sheetTitle' => __('Dispatch report'),
                'data' => $dataExcel->toArray(),
                'type' => 'routeReportUngrouped'
            ];
            /* SHEETS */
            $excel = PCWExporterService::createHeaders($excel, $dataExport);
            $excel = PCWExporterService::createSheet($excel, $dataExport);
        });

        if ($store) {
            $excelFile->store($fileExtension);
            return "$excelFile->storagePath/$fileName.$fileExtension";
        }

        return $excelFile->export($fileExtension);
    }

    /**
     * @param $eventsReports
     * @param $dateReport
     * @param bool $store
     * @return Collection
     */
    function eventsRouteReport($eventsReports, $dateReport, $store = false)
    {
        $pathsToConsolidatesReportFiles = collect([]);

        $fileName = str_replace([' ', '-'], '_', __('Events report') . "_" . $dateReport);
        $fileExtension = 'xlsx';

        $excel = Excel::create($fileName, function ($excel) use ($eventsReports, $fileName, $fileExtension) {

            foreach ($eventsReports as $eventsReport) {
                $route = $eventsReport->route;
                $company = $route->company;
                $date = $eventsReport->date;

                $fileNameSheet = __('Events report') . " $route->name" . " $date";

                if ($eventsReport->totalReports) {
                    $reportVehicleByRoute = $eventsReport->reportVehicleByRoute;


                    $dataExcel = array();
                    $typeReport = 'consolidatedRouteReport';

                    foreach ($reportVehicleByRoute as $reportByVehicle) {
                        $vehicle = $reportByVehicle->vehicle;
                        $dispatchRegister = $reportByVehicle->dispatchRegister;
                        $driver = $dispatchRegister->driver;

                        $details = $this->buildStringDetails($reportByVehicle);

                        $link = route('link-report-route-chart-view', ['dispatchRegister' => $dispatchRegister->id, 'location' => 0]);


                        $dataExcelColumns = collect([
                            __('Turn') => $dispatchRegister->turn,                                                          # A CELL
                            __('Round Trip') => $dispatchRegister->round_trip,                                              # B CELL
                            __('Vehicle') => $vehicle->number,                                                              # C CELL
                            __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                        # D CELL
                            __('Off Roads') => $reportByVehicle->totalOffRoads,                                             # E CELL
                            __('Off roads details') => "$details->offRoadReportString",                                     # F CELL
                            __('Speeding') => $reportByVehicle->totalSpeeding,                                              # G CELL
                            __('Speeding details') => $details->speedingReportString,                                       # H CELL
                        ]);

                        if ($company->hasControlPointEventsActive()) {
                            $dataExcelColumns->put(__('Delay control points'), $reportByVehicle->controlPointReportTotal);  # I CELL
                            $dataExcelColumns->put(__('Control points details'), $details->delayControlPointsReportString); # J CELL
                            $typeReport = 'consolidatedRouteReportWithControlPoint';
                        }


                        $dataExcelColumns->put(__('Details'), $link);                                                       # I/K CELL

                        $dataExcel[] = $dataExcelColumns->toArray();
                    }

                    if (count($dataExcel)) {
                        $dataExport = (object)[
                            'fileName' => $fileNameSheet,
                            'title' => __('Events report') . " $route->name",
                            'subTitle' => "$date",
                            'sheetTitle' => "$route->name",
                            'data' => $dataExcel,
                            'type' => $typeReport
                        ];

                        /* SHEETS */
                        $excel = PCWExporterService::createHeaders($excel, $dataExport);
                        $excel = PCWExporterService::createSheet($excel, $dataExport);
                    }


                } else {
                    dump("No reports found for $route->name on date $date");
                }
            }
        });

        if ($store) {
            $excel->store($fileExtension);
            $pathsToConsolidatesReportFiles->push("$excel->storagePath/$fileName.$fileExtension");
            return $pathsToConsolidatesReportFiles;
        }

        return $excel->export($fileExtension);
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

    /**
     * Export excel by Vehicle option
     *
     * @param Collection $managementReports
     * @param $dateReport
     * @param bool $store
     * @return string
     * @internal param $roundTripDispatchRegisters
     */
    public function exportManagementReport($managementReports, $dateReport, $store = false)
    {
        $fileName = str_replace([' ', '-'], '_', __('Management report') . " $dateReport");
        $fileExtension = 'xlsx';

        $excelFile = Excel::create($fileName, function ($excel) use ($managementReports, $dateReport) {
            $dataExcel = collect([]);

            foreach ($managementReports as $vehicleId => $managementReport) {
                $vehicle = $managementReport->vehicle;
                $driver = $managementReport->driver;
                $company = $vehicle->company;
                $dispatchRoutes = $managementReport->dispatchRoutes;

                $dispatchRoutesTurns = "";
                $index = 0;
                foreach ($dispatchRoutes as $dispatchRoute){
                    $ln = $index > 0 ? "\n" : "";
                    $route = $dispatchRoute->route;
                    $roundTrips = $dispatchRoute->dispatchRegisters->max('round_trip');
                    $dispatchRoutesTurns .= "$ln$route->name | $roundTrips ".($company->isIntermunicipal() ? __('turns') : __('round trips'));
                    $index++;
                }

                $data = collect([
                    __('Vehicle') => $vehicle->number,                                                                  # A CELL
                    __('Turns') => $dispatchRoutesTurns,                                                                # B CELL
                    __('Total round trips') => $managementReport->totalRoundTrips,                                      # C CELL
                    __('Total route time') => $managementReport->totalRouteTime,                                        # D CELL
                    __('Total off roads') => $managementReport->totalOffRoads ? $managementReport->totalOffRoads : '',  # E CELL
                    __('Total speeding') => $managementReport->totalSpeeding ? $managementReport->totalSpeeding : '',   # F CELL
                    __('Max speed') => $managementReport->maxSpeed ? $managementReport->maxSpeed : '',                  # G CELL
                    __('Max speed time') => $managementReport->maxSpeedTime,                                            # H CELL
                    __('Mileage') => $managementReport->mileage ? number_format($managementReport->mileage/1000, 2, ',', '.') : '',                                                        # I CELL
                    __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                            # J CELL
                ]);

                $dataExcel->push($data->toArray());
            }

            $dataExport = (object)[
                'fileName' => __('Management report') . " $dateReport",
                'title' => __('Management report'),
                'subTitle' => __('Date')." $dateReport",
                'sheetTitle' => __('Management report'),
                'data' => $dataExcel->toArray(),
                'type' => 'managementReport'
            ];

            /* SHEETS */
            $excel = PCWExporterService::createHeaders($excel, $dataExport);
            $excel = PCWExporterService::createSheet($excel, $dataExport);
        });

        if ($store) {
            $excelFile->store($fileExtension);
            return "$excelFile->storagePath/$fileName.$fileExtension";
        }

        return $excelFile->export($fileExtension);
    }

    /**
     * @param Collection $currentVehicleStatusReport
     * @param bool $store
     * @return string
     */
    public function exportCurrentVehicleStatusReport($currentVehicleStatusReport, $store = false)
    {
        $now = Carbon::now();
        $fileName = str_replace([' ', '-'], '_', __('Vehicle status') . " ".$now->toDateTimeString());
        $fileExtension = 'xlsx';

        $excelFile = Excel::create($fileName, function ($excel) use ($currentVehicleStatusReport, $now) {
            $dataExcel = collect([]);

            $currentVehicleStatusReport = $currentVehicleStatusReport->sortBy(function($vehicleStatusReport){
                $vehicleStatus = $vehicleStatusReport->vehicleStatus;
                return $vehicleStatus ? $vehicleStatus->order : '';
            });

            foreach ($currentVehicleStatusReport as $vehicleStatusReport) {
                $vehicle = $vehicleStatusReport->vehicle;
                $vehicle = Vehicle::find($vehicle->id);
                $dispatcherVehicle = $vehicleStatusReport->dispatcherVehicle;
                $currentDispatchRegister = $vehicleStatusReport->currentDispatchRegister;
                $currentLocation = $vehicleStatusReport->currentLocation;
                $vehicleStatus = $vehicleStatusReport->vehicleStatus;

                $currentRoute = '';
                if( $dispatcherVehicle ) $currentRoute =  $dispatcherVehicle->route->name;
                else if( $currentDispatchRegister ) $currentRoute = $currentDispatchRegister->route->name;

                $vehicleObservations = "";
                if($vehicle->in_repair){
                    $vehicleObservations .= __('In repair').($vehicle->observations ? "\n".$vehicle->observations : '');
                }

                $data = collect([
                    __('#') => $dataExcel->count() + 1,                                                                  # A CELL
                    __('Vehicle') => $vehicle->number,                                                                  # A CELL
                    __('Status') => $vehicleStatus->des_status ?? '---',                                                                # B CELL
                    __('Observations') => $vehicleObservations,                                                                  # A CELL
                    __('Last report') => $currentLocation ? $currentLocation->date->toDateTimeString() : '',                                    # C CELL
                    __('Mileage') => $currentLocation ? number_format($currentLocation->current_mileage/1000, 2, ',', '.') : '',                                        # D CELL
                    __('Route') => $currentRoute,                                        # D CELL
                ]);

                $dataExcel->push($data->toArray());
            }

            $dataExport = (object)[
                'fileName' => __('Vehicle status') . " ".$now->toDateTimeString(),
                'title' => __('Vehicle status'),
                'subTitle' => __('Date')." ".$now->toDateTimeString(),
                'sheetTitle' => __('Vehicle status'),
                'data' => $dataExcel->toArray(),
                'type' => 'currentVehicleStatusReport'
            ];

            /* SHEETS */
            $excel = PCWExporterService::createHeaders($excel, $dataExport);
            $excel = PCWExporterService::createSheet($excel, $dataExport);
        });

        if ($store) {
            $excelFile->store($fileExtension);
            return "$excelFile->storagePath/$fileName.$fileExtension";
        }

        return $excelFile->export($fileExtension);
    }
}