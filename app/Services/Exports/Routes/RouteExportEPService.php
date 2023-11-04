<?php


namespace App\Services\Exports\Routes;


use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\DrObservation;
use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;
use App\Services\Exports\PCWExporterEPService;
use App\Traits\CounterByRecorder;
use Auth;
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
    public function groupedRouteReport($vehiclesDispatchRegisters, $dateReport, $dateEndReport = null, $store = false)
    {
        $fileName = __('DR') . " $dateReport $dateEndReport";
        $fileName = str_replace('-', '', str_replace(' ', '_', $fileName));

        $excelFile = Excel::create($fileName, function ($excel) use ($vehiclesDispatchRegisters, $dateReport, $dateEndReport) {

            foreach ($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters) {
                $vehicle = Vehicle::find($vehicleId);
                $vehicleCounter = CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegisters);
                $dataExcel = array();
                $lastArrivalTime = null;
                $totalDeadTime = '00:00:00';
                $nameRute = "";

                foreach ($dispatchRegisters as $iteration => $dispatchRegister) {
                    $historyCounter = $vehicleCounter->report->history[$dispatchRegister->id];
                    $route = $dispatchRegister->route;
                    $totalRoundTrip = $historyCounter->passengersByRoundTrip;
                    $deadTime = $lastArrivalTime ? StrTime::subStrTime($dispatchRegister->departure_time, $lastArrivalTime) : '';
                    $drObservation = $dispatchRegister->getObservation('spreadsheet_passengers');
                    $spreadsheet = $drObservation->observation;
                    $passengerSpreadsheet =(int) $drObservation->value;
                    $spreadsheetPassengersSync = $dispatchRegister->getObservation('spreadsheet_passengers_sync')->value;
                    $username = $drObservation->user ? $drObservation->user->name : '';
                    $roundTrip = $iteration + 1;
                    $nameRute = "";
                    $tariffPassenger = $dispatchRegister->route->tariff->passenger;
                    $routeId = $dispatchRegister->route_id;

                    switch ($routeId){
                        case $routeId ==279 || $routeId ==280:
                            $nameRute = "RUTA PALMIRA";
                            break;
                        case $routeId ==272 || $routeId ==271:
                            $nameRute = "RUTA PALMIRA ";
                            break;
                        case  $routeId == 282 || $routeId == 283:
                            $nameRute = "RUTA AEROPUERTO";
                            break;
                    }
                    $timeFrange=$dispatchRegister->departure_time;
                    $promPassengers = 0;
                    $routeProm = $dispatchRegister->route_id;
                    switch (true) {
                        case ($timeFrange >= '04:00:00' && $timeFrange <= '06:00:59'):
                            if ($routeProm==279||$routeProm==280){
                                $promPassengers = 10;
                            }else if ($routeProm==282){
                                $promPassengers = 3;
                            }else if ($routeProm==283){
                                $promPassengers = 27;
                            }
                            break;
                        case ($timeFrange >= '06:01:00' && $timeFrange <= '09:00:51'):
                            if ($routeProm==279||$routeProm==280){
                                $promPassengers = 22;
                            }else if ($routeProm==282){
                                $promPassengers = 3;
                            }else if ($routeProm==283){
                                $promPassengers = 27;
                            }
                            break;
                        case ($timeFrange >= '09:01:00' && $timeFrange <= '11:00:59'):
                            if ($routeProm==279||$routeProm==280){
                                $promPassengers = 18;
                            }else if ($routeProm==282){
                                $promPassengers = 3;
                            }else if ($routeProm==283){
                                $promPassengers = 27;
                            }
                            break;
                        case ($timeFrange >= '11:01:00' && $timeFrange <= '14:00:59'):
                            if ($routeProm==279||$routeProm==280){
                                $promPassengers = 19;
                            }else if ($routeProm==282){
                                $promPassengers = 3;
                            }else if ($routeProm==283){
                                $promPassengers = 27;
                            }
                            break;
                        case ($timeFrange >= '14:01:00' && $timeFrange <= '17:00:59'):
                            if ($routeProm==279||$routeProm==280){
                                $promPassengers = 20;
                            }else if ($routeProm==282){
                                $promPassengers = 3;
                            }else if ($routeProm==283){
                                $promPassengers = 27;
                            }
                            break;
                        case ($timeFrange >= '17:01:00' && $timeFrange <= '20:00:59'):
                            if ($routeProm==279||$routeProm==280){
                                $promPassengers = 21;
                            }else if ($routeProm==282){
                                $promPassengers = 3;
                            }else if ($routeProm==283){
                                $promPassengers = 27;
                            }
                            break;
                        case ($timeFrange >= '20:01:00' && $timeFrange <= '23:59:59'):
                            if ($routeProm==279||$routeProm==280){
                                $promPassengers = 13;
                            }else if ($routeProm==282){
                                $promPassengers = 3;
                            }else if ($routeProm==283){
                                $promPassengers = 27;
                            }
                            break;
                    }
                    $spreadsheetPassengers1 =(int) $dispatchRegister->getObservation('spreadsheet_passengers')->value;
                    $TotalSystema = 0;

                    $topologies = \App\Models\Vehicles\TopologiesSeats::query() //total asientos de VH
                    ->where('vehicle_id', $vehicle->id)
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
                    if ($dispatchRegister->final_sensor_counter <= $spreadsheetPassengers1){
                        $totalPassengersAE = $spreadsheetPassengers1;
                    }elseif ($dispatchRegister->final_sensor_counter >= $promPassengers){
                        $totalPassengersAE =$dispatchRegister->final_sensor_counter;
                    }else{
                        $totalPassengersAE = $dispatchRegister->final_sensor_counter;
                    }
                    $countMax = $dispatchRegister->final_front_sensor_counter;
                    $countMaxAssets = $countMax>=$totalSeats ? $totalSeats : $countMax;
                    $totalPassengers = $countMaxAssets >= $spreadsheetPassengersSync ? $countMaxAssets : $spreadsheetPassengersSync;

                    if ($routeProm==279||$routeProm==280||$routeProm==282||$routeProm==283){
                        $TotalSystema = $totalPassengersAE>$totalSeats ? $totalSeats ?? 0 : $totalPassengersAE ?? 0;
                    }else{
                        $TotalSystema = $totalPassengers ? $totalPassengers : 0;
                    }




                    if ($dispatchRegister->final_sensor_counter <= $spreadsheetPassengers1){
                        $TotalSystema = (int)$spreadsheetPassengers1;
                    }elseif ($dispatchRegister->final_sensor_counter >= $promPassengers){
                        $TotalSystema = (int)$dispatchRegister->final_sensor_counter;
                    }elseif ($dispatchRegister->final_sensor_counter<= $promPassengers){
                        $TotalSystema = (int)$dispatchRegister->final_sensor_counter;
                    }

                    if (Auth::user()->isSuperAdmin()){
                        $dataExcel[] = [
                            __('Date') => $dispatchRegister->date,                                                          # A CELL
                            __('Route') => $route->name,                                                                    # B CELL
                            __('Round Trip') => $roundTrip,                                                                 # C CELL
                            __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # D CELL
                            __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # E CELL
                            __('Route Time') => $dispatchRegister->getRouteTime(),                                          # F CELL
                            __('Status') => $dispatchRegister->status,                                                      # G CELL
                            __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),                           # H CELL
                            __('Valor pasaje') => '',                                                                       # I CELL
                            __('N° planilla') => $spreadsheet ?: "",                                                              # J CELL
                            __('Pasajeros planilla') => $passengerSpreadsheet,                                                              # J CELL
                            __('#sensor') => $dispatchRegister->final_sensor_counter,                                                                     # K CELL
                            __('Promedio') =>"$promPassengers",                                                                     # K CELL
                            __('Total Sistema') =>$TotalSystema,                                                                     # K CELL
                            __('Conteo Maximos') =>$dispatchRegister->final_front_sensor_counter,                                                                     # K CELL
                        ];
                    }else{
                        $dataExcel[] = [
                            __('Date') => $dispatchRegister->date,                                                          # A CELL
                            __('Route') => $route->name,                                                                    # B CELL
                            __('Round Trip') => $roundTrip,                                                                 # C CELL
                            __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # D CELL
                            __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # E CELL
                            __('Route Time') => $dispatchRegister->getRouteTime(),                                          # F CELL
                            __('Status') => $dispatchRegister->status,                                                      # G CELL
                            __('Pass.') . " " . __('visual') => intval($totalRoundTrip),                           # H CELL
                            __('Valor pasaje') => '',
                            __('Total sistema') => $TotalSystema,
                            __('FICS') => $spreadsheetPassengersSync ?? 0,
                            __('N° planilla') => $spreadsheet ?: "",                                                              # J CELL
                            __('Usuario') => $username,
                        ];
                    }



                    $totalDeadTime = $deadTime ? StrTime::addStrTime($totalDeadTime, $deadTime) : $totalDeadTime;

                    $lastArrivalTime = $dispatchRegister->arrival_time;
                }

                $dateEndTitle = $dateEndReport ? "- $dateEndReport" : "";
                $dataExport = (object)[
                    'fileName' => __('Dispatch report') . " V $dateReport",
                    'title' => __('Dispatch report') . " | $dateReport $dateEndTitle",
                    'subTitle' => "$vehicle->number | $vehicle->plate",
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'routeReportByVehicle',
                    'tariff' => $tariffPassenger,
                    'nameRute' => $nameRute
                ];

                /* SHEETS */
                $excel = PCWExporterEPService::createHeaders($excel, $dataExport);
                $excel = PCWExporterEPService::createSheet($excel, $dataExport);
            }
        });

        $fileExtension = 'xlsx';

        if ($store) {
            $excelFile->store($fileExtension);
            return "$excelFile->storagePath/$fileName.$fileExtension";
        }

        return $excelFile->download($fileExtension);
    }
}