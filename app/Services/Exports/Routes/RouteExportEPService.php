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
    public function groupedRouteReport($vehiclesDispatchRegisters, $dateReport, $dateEndReport = null, $store = false, $exportFiCS = false)
    {
        $fileName = __('DR') . " $dateReport $dateEndReport";
        $fileName = str_replace('-', '', str_replace(' ', '_', $fileName));
        \Log::info("Iniciando groupedRouteReport para: $fileName");
        try

        {
            \Log::info("Dentro de la closure de Excel::create");
            $excelFile = Excel::create($fileName, function ($excel) use ($vehiclesDispatchRegisters, $dateReport, $dateEndReport, $exportFiCS) {
                $tariffPassenger =[];

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
                        $passengersStopsFICS = json_decode($dispatchRegister->getObservation('passengers_stops')->observation,true);
                        $username = $drObservation->user ? $drObservation->user->name : '';
                        $roundTrip = $iteration + 1;
                        //$nameRute = "";

                        $tariffPassenger = $dispatchRegister->route->tariff->passenger;
                        if (in_array($dispatchRegister->route->id, [280,279,276,275])) {
                            if (in_array($dispatchRegister->date, ["2025-12-31", "2025-12-30","2025-12-29", "2025-12-28","2025-12-27","2025-12-26","2025-12-25","2025-12-24"])){
                                $tariffPassenger=5600;
                            }
                        }
                        if (in_array($dispatchRegister->route->id, [282,283])) {
                            if (in_array($dispatchRegister->date, ["2025-12-31", "2025-12-30","2025-12-29", "2025-12-28","2025-12-27","2025-12-26","2025-12-25","2025-12-24"])){
                                $tariffPassenger=14000;
                            }
                        }

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


                        if (Auth::user()->isSuperAdmin() && $exportFiCS == false){
                            $dataExcel[] = [
                                __('Date') => $dispatchRegister->date,                                                          # A CELL
                                __('Route') => $route->name,                                                                    # B CELL
                                __('Round Trip') => $roundTrip,                                                                 # C CELL
                                __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # D CELL
                                __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # E CELL
                                __('Route Time') => $dispatchRegister->getRouteTime(),                                          # F CELL
                                __('Status') => $dispatchRegister->status,                                                       # G CELL
                                __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),                             # H CELL
                                __('Valor pasaje') => intval($totalRoundTrip) * $tariffPassenger,                                # I CELL
                                __('N° planilla') => $spreadsheet ?: "",                                                         # J CELL
                                __('Pasajeros planilla') => $passengerSpreadsheet,                                               # J CELL
                                __('#sensor') => $dispatchRegister->final_sensor_counter,                                        # K CELL
                                __('Promedio') =>"$promPassengers",                                                              # K CELL
                                __('Total Sistema') =>$TotalSystema,                                                             # K CELL
                                __('Conteo Maximos') =>$dispatchRegister->final_front_sensor_counter,                            # K CELL
                            ];
                        }elseif ($exportFiCS && !empty($passengersStopsFICS))
                        {
                            foreach ($passengersStopsFICS as $stop => $values) {
                                $dataExcel[] = [
                                    'Parada' => $stop,
                                    'Ascienden' => $values['a'],
                                    'Descienden' => $values['d'],
                                    'Hora' => $values['time'],
                                ];
                            }
                            $dataExcel[] = [
                                'Parada' => '',
                                'Ascienden' => '',
                                'Descienden' => '',
                                'Hora' => '',
                            ];
                            $dataExcel[] = [
                                'Parada' => '',
                                'Ascienden' => '',
                                'Descienden' => '',
                                'Hora' => '',
                            ];
                        }
                        else if (Auth::user()->id == 2018101286){
                            $dataExcel[] = [
                                __('Date') => $dispatchRegister->date,                                                          # A CELL
                                __('Route') => $route->name,                                                                    # B CELL
                                __('Round Trip') => $roundTrip,                                                                 # C CELL
                                __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # D CELL
                                __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # E CELL
                                __('Route Time') => $dispatchRegister->getRouteTime(),                                          # F CELL
                                __('Status') => $dispatchRegister->status,                                                      # G CELL
                                __('Pass.') . " " . __('Visual') => intval($totalRoundTrip),                           # H CELL
                                __('Valor pasaje') => intval($totalRoundTrip) * $tariffPassenger,
                                __('Pasajeros Planilla') => $spreadsheetPassengers1 ?? 0,
                                __('FICS') => $spreadsheetPassengersSync ?? 0,
                                __('N° planilla') => $spreadsheet ?: "",                                                              # J CELL
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
                                __('Valor pasaje') => intval($totalRoundTrip) * $tariffPassenger,
                                __('Total sistema') => $TotalSystema,
                                __('FICS') => $spreadsheetPassengersSync ?? 0,
                                __('N° planilla') => $spreadsheet ?: "",                                                              # J CELL
                                __('Usuario') => $username,
                                __('id') => $route->id
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
                        'nameRute' => $nameRute,
                        'routeID' =>$route->id,
                        'exportFiCS' =>$exportFiCS
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
        }catch (\Exception $e) {
            \Log::error("Error EXCEPCIONAL en groupedRouteReport: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            // Devuelve una respuesta de error JSON o una vista de error para verla en el navegador
            // Esto es MEJOR que dejar que el script muera y obtener ERR_INVALID_RESPONSE
            if (request()->expectsJson()) { // O alguna otra forma de detectar si se espera JSON
                return response()->json(['error' => 'Ocurrió un error generando el reporte.', 'message' => $e->getMessage()], 500);
            }
            // O redirige a una página de error, o muestra un mensaje simple.
            // ¡Importante! Asegúrate de que el controlador `show` capture y devuelva esta respuesta.
            throw $e; // Relanza la excepción para que el controlador la maneje o se loguee más arriba si prefieres
        }
    }
}