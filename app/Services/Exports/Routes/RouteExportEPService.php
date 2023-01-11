<?php


namespace App\Services\Exports\Routes;


use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\DrObservation;
use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;
use App\Services\Exports\PCWExporterEPService;
use App\Traits\CounterByRecorder;
use Excel;

class RouteExportEPService extends RouteExportService
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

                foreach ($dispatchRegisters as $iteration => $dispatchRegister) {
                    $historyCounter = $vehicleCounter->report->history[$dispatchRegister->id];
                    $route = $dispatchRegister->route;
                    $totalRoundTrip = $historyCounter->passengersByRoundTrip;
                    $deadTime = $lastArrivalTime ? StrTime::subStrTime($dispatchRegister->departure_time, $lastArrivalTime) : '';

                    $drObservation = $dispatchRegister->getObservation('registradora_llegada');

                    $spreadsheet = $drObservation->observation;
                    $username = $drObservation->user ? $drObservation->user->name : '';

                    //$roundTrip = $dispatchRegister->round_trip;
                    $roundTrip = $iteration + 1;

                    $tarifRoute=null;
                    $routeId= $dispatchRegister->route_id;
                    if ($routeId==279||$routeId==280){
                        $tarifRoute=4800;
                    }elseif ($routeId==282||$routeId==283){
                        $tarifRoute=11000;
                    }else{
                        $tarifRoute="Parametrizar valor pasaje";
                    }

                    $dataExcel[] = [
                        __('Date') => $dispatchRegister->date,                                                          # A CELL
                        __('Route') => $route->name,                                                                    # B CELL
                        __('Round Trip') => $roundTrip,                                                                 # C CELL
                        __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # D CELL
                        __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # E CELL
                        __('Route Time') => $dispatchRegister->getRouteTime(),                                          # F CELL
                        __('Status') => $dispatchRegister->status,                                                      # G CELL
                        __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),                           # H CELL
                        __('Valor pasaje') => $tarifRoute,                                                                       # I CELL
                        __('N° planilla') => $spreadsheet,                                                              # J CELL
                        __('Usuario') => $username,                                                                     # K CELL
                    ];

                    $totalDeadTime = $deadTime ? StrTime::addStrTime($totalDeadTime, $deadTime) : $totalDeadTime;

                    $lastArrivalTime = $dispatchRegister->arrival_time;
                }

                $dataExport = (object)[
                    'fileName' => __('Dispatch report') . " V $dateReport",
                    'title' => __('Dispatch report') . " | $dateReport",
                    'subTitle' => "$vehicle->number | $vehicle->plate" . ". ",
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'routeReportByVehicle'
                ];

                /* SHEETS */
                $excel = PCWExporterEPService::createHeaders($excel, $dataExport);
                $excel = PCWExporterEPService::createSheet($excel, $dataExport);
            }
        })->download('xlsx');
    }
}