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

                foreach ($dispatchRegisters as $dispatchRegister) {
                    $historyCounter = $vehicleCounter->report->history[$dispatchRegister->id];
                    $route = $dispatchRegister->route;
                    $totalRoundTrip = $historyCounter->passengersByRoundTrip;
                    $idDispatch = $dispatchRegister->id;
                    $deadTime = $lastArrivalTime ? StrTime::subStrTime($dispatchRegister->departure_time, $lastArrivalTime) : '';

                    $spreadsheet = DrObservation::where('dispatch_register_id', $idDispatch)->get()->pluck('observation')->first();
                    $user = DrObservation::where('dispatch_register_id', $idDispatch)->get()->pluck('user_id')->first();
                    $username = User::where('id', $user)->get()->pluck('name')->first();

                    $dataExcel[] = [
                        __('Date') => $dispatchRegister->date,                                                          # A CELL
                        __('Route') => $route->name,                                                                    # B CELL
                        __('Round Trip') => $dispatchRegister->round_trip,                                              # C CELL
                        __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # D CELL
                        __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # E CELL
                        __('Route Time') => $dispatchRegister->getRouteTime(),                                          # F CELL
                        __('Status') => $dispatchRegister->status,                                                      # G CELL
                        __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),                           # H CELL
                        __('Valor pasaje') => '',                                                                       # I CELL
                        __('N° planilla') => $spreadsheet,                                                              # J CELL
                        __('usuario') => $username,                                                                     # K CELL
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
                $excel = PCWExporterEPService::createHeaders($excel, $dataExport);
                $excel = PCWExporterEPService::createSheet($excel, $dataExport);
            }
        })->download('xlsx');
    }
}