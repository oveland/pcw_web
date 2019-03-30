<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatcherVehicle;
use App\Models\Routes\Route;
use App\Models\Vehicles\CurrentLocationsGPS;
use App\Models\Vehicles\GpsVehicle;
use App\Http\Controllers\API\SMS;
use App\Models\Vehicles\SimGPS;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleStatus;
use Auth;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Storage;

class ManagerGPSController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }

        $routes = Route::where('company_id', 21)->get();
        return view('admin.gps.manage.index', compact(['companies', 'routes']));
    }
  
    public function list(Request $request)
    {
        $companyReport = $request->get('company-report');
        $gpsReport = $request->get('gps-report');
        $optionSelection = $request->get('option-selection');
        $routeReport = $request->get('route-report');
        $isInLimbo = $request->get('limbo') == "si" ? true : false;

        $dispatcherVehicle = $routeReport != 'all' ? DispatcherVehicle::where('route_id', $routeReport)->get() : collect([]);

        $simGPSList = null;
        if ($companyReport != 'any') {
            $company = (Auth::user()->isAdmin()) ? Company::find($companyReport) : Auth::user()->company;
            $vehiclesCompany = $company->vehicles;
            $simGPSList = SimGPS::whereIn('vehicle_id', $vehiclesCompany->pluck('id'));

            $simGPSList = ($gpsReport != 'all') ? $simGPSList->where('gps_type', $gpsReport)->get() : $simGPSList->get();

            if( $isInLimbo ){
                $gpsLimbo = collect(['7', '9', '10', '23', '24', '2000', '2038', '2066', '2070', '2182', '2342', '2346', '2347', '2349', '2406', '2420', '2427', '2430', '2448', '2477', '2484', '4402', '4455', '4456', '4466', '4483', '4484', '4486', '4492', '4516', '4559']);
                $simGPSList = $simGPSList->filter(function ($simGPS) use ($gpsLimbo) {
                    return $gpsLimbo->contains($simGPS->vehicle->number);
                });
            }

            $simGPSList = $simGPSList->sortBy(function ($simGPS) {
                return $simGPS->vehicle->number ?? true;
            });


            $unAssignedVehicles = $vehiclesCompany
                ->where('active', true)
                ->whereNotIn('id', SimGPS::whereIn('vehicle_id', $vehiclesCompany->pluck('id'))->get()->pluck('vehicle_id'))
                ->sortBy(function ($vehicle) {
                    return $vehicle->number;
                });

            $selection = array();
            switch ($optionSelection) {
                case 'all':
                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        if($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count()) $selection[] = $simGPS->vehicle->number;
                    }
                    break;
                case 'no-report':

                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id)->get()->first();
                        if( $currentLocationGPS ){
                            try{
                                $vehicleStatus = $currentLocationGPS->vehicleStatus;
                            }catch (\Exception $exception){
                                dd($currentLocationGPS);
                            }
                            if ($vehicleStatus->id == VehicleStatus::NO_REPORT && ($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count())) $selection[] = $simGPS->vehicle->number;
                        }
                    }
                    break;
                case 'ok':
                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id)->get()->first();
                        if( $currentLocationGPS ){
                            $vehicleStatus = $currentLocationGPS->vehicleStatus;
                            if ($vehicleStatus->id != VehicleStatus::NO_REPORT) $selection[] = $simGPS->vehicle->number;
                        }
                    }
                    break;

                case 'new':
                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        if( !$vehicle || !$vehicle->currentLocation && ($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count()) )$selection[] = $simGPS->vehicle->number;
                    }
                    break;
                default:
                    $selection = [];
                    break;
            }
        }

        return view('admin.gps.manage.list', compact(['simGPSList', 'vehiclesCompany', 'selection', 'optionSelection', 'unAssignedVehicles', 'gpsReport']));
    }

    public function getVehicleStatus(Request $request)
    {
        $simGPSList = $request->get('simGPSList');

        $statusList = "";
        $reportsStatus = collect([]);
        $totalOK = 0;
        $index = 1;

        foreach ($simGPSList as $sim) {
            $simGPS = SimGPS::where('sim', $sim)->get()->first();
            $vehicle = $simGPS->vehicle;
            $currentLocationGPS = CurrentLocationsGPS::where('vehicle_id', $vehicle->id)->get()->first() ?? null;

            dd($currentLocationGPS);

            $classStatus = null;
            if($currentLocationGPS){
                $vehicleStatus = $currentLocationGPS->vehicleStatus;
                $timePeriod = $currentLocationGPS->getTimePeriod();

                if ($vehicleStatus->id == VehicleStatus::OK || $vehicleStatus->id == VehicleStatus::PARKED || $vehicleStatus->id == VehicleStatus::POWER_OFF){
                    $classStatus = "bg-lime p-2 text-white text-bold";
                    $totalOK++;
                }

                $statusList .= $vehicleStatus ? "<a href='tel:$simGPS->sim' class='tooltips click col-md-12' title='$vehicleStatus->des_status' data-placement='left' style='border: 1px solid grey;height: 30px;padding: 5px;'><i class='text-$vehicleStatus->main_class $vehicleStatus->icon_class' style='width: 15px'></i> <span class='' style='width: 20px; border-radius: 5px'>$vehicle->number</span> $currentLocationGPS->date (<span class=''>$timePeriod</span>)</a><br><br>" : "********";

                $reportsStatus->push((object)[
                    'statusId' => $vehicleStatus->id,
                    'status' => $vehicleStatus
                ]);
            }

            $index++;
        }

        $headerReport = "";
        $reportsByStatus = $reportsStatus->sortBy('statusId')->groupBy('statusId');
        foreach ($reportsByStatus as $statusId => $reportStatus) {
            if ($reportStatus->first()) {
                $status = $reportStatus->first()->status;
                $headerReport .= "<span class='tooltips click btn btn-$status->main_class btn-sm' style='border-radius: 0' title='$status->des_status' data-placement='bottom'><i class='$status->icon_class' style='width: 15px'></i> <strong>" . count($reportStatus) . "</strong></span>";
            }
        }

        return "<div class='text-center'>GPS OK $totalOK of " . count($simGPSList) . " in total<br>$headerReport</div> <hr class='m-t-5 m-b-5'>$statusList";
    }

    public function sendSMS(Request $request)
    {
        $simGPSList = $request->get('sim-gps');

        $simGPSNumbers = is_array($simGPSList) ? $simGPSList : explode(";", $simGPSList);
        $now = Carbon::now();
        foreach ($simGPSNumbers as $sim) {
            $dump = "************** $now >> $sim **************\n";
            $commands = $request->get('command-gps');
            $gpsCommands = explode("\n", $commands);

            switch ($request->get('gps-type')) {
                case SimGPS::SKYPATROL_OLD:
                    $smsCommands = [];

                    foreach ($gpsCommands as $gpsCommand) {
                        $gpsCommand = explode(";", $gpsCommand);
                        $simGPS = SimGPS::findBySim($sim);
                        if($simGPS && $simGPS->gps){
                            foreach ($gpsCommand as &$c){
                                if( str_contains($c, "ID=") ){
                                    $imei = $simGPS->gps->imei;
                                    $c = "ID=$imei";
                                }
                            }
                            $smsCommands[] = implode(";", $gpsCommand);
                        }
                    }

                    $gpsCommands = $smsCommands;
                    break;
                case SimGPS::SKYPATROL:
                    $totalCMD = "";
                    $smsCommands = [];

                    if ($commands != 'AT&W' && $commands != 'AT$RESET') {
                        $flagStartCMD = true;
                        foreach ($gpsCommands as $gpsCommand) {
                            $individualCommand = trim(explode("'", $gpsCommand)[0]);
                            if ($individualCommand) {
                                $individualCommand = str_replace(["AT", "At", "aT", "at"], "", $individualCommand);

                                // Checks for auto set plate
                                if (str_contains($individualCommand, "TTDEVID")) {
                                    $simGPS = SimGPS::findBySim($sim);
                                    if ($simGPS) {
                                        if ($request->get('auto-set-plate')) {
                                            $vehicle = $simGPS->vehicle;
                                            $individualCommand = '$TTDEVID="' . $vehicle->plate . '"';
                                            dump(" - Auto set plate for: $vehicle->plate");
                                        }
                                    } else {
                                        dd("Error: Está intentando establecer una placa que no existe en la configuración de SIM-GPS: $individualCommand");
                                    }

                                    if (!$request->get('auto-set-plate') && count($simGPSNumbers) > 1) {
                                        dd("Error: No es posible establecer la misma placa para varios vehículos. Seleccione la opción 'Auto setear placa'");
                                    }
                                }
                                if ($individualCommand) {
                                    if (strlen($totalCMD) + strlen($individualCommand) + 2 < config('sms.sms_max_length_for_gps')) {
                                        $totalCMD .= ($flagStartCMD ? "" : ";") . $individualCommand;
                                        $flagStartCMD = false;
                                    } else {
                                        $smsCommands[] = str_start($totalCMD, "AT") . ";&W";
                                        $totalCMD = $individualCommand . ";";
                                        $flagStartCMD = true;
                                    }
                                }
                            }
                        }
                        $smsCommands[] = trim(str_start($totalCMD, "AT") . ";&W");

                        $gpsCommands = $smsCommands;
                    }
                    break;
                case SimGPS::COBAN:
                    $smsCommands = [];
                    $smsCommands2 = [];
                    foreach ($gpsCommands as $gpsCommand) {
                        $smsCommands[] = trim($gpsCommand);
                    }
                    $gpsCommands = $smsCommands;
                    break;
                case SimGPS::RUPTELA:
                    $smsCommands = [];
                    foreach ($gpsCommands as $gpsCommand) {
                        $command = trim($gpsCommand);
                        if( !starts_with($gpsCommand, ' ') ){
                            $command = " $command";
                        }
                        $smsCommands[] = $command;
                    }

                    $gpsCommands = $smsCommands;
                    break;
            }

            $totalSent = 0;
            foreach ($gpsCommands as $smsCommand) {
                $totalSent++;
                $responseSMS = SMS::sendCommand($smsCommand, $sim);
                $length = strlen($smsCommand);

                $dump .= ("$smsCommand \n $length Chars (" . ($responseSMS['resultado'] === 0 ? "successfully" : "error") . ")") . "\n\n";
                sleep(0.5);
            }
            $dump .= "-------------- TOTAL SMS SENT: $totalSent --------------\n";
            dump($dump);
        }

        dd("................................................");
    }

    public function createSIMGPS(Request $request)
    {
        $transaction = \DB::transaction(function () use ($request){
            $sim = $request->get('sim');
            $gpsType = $request->get('gps_type');
            $imei = $request->get('imei');

            $vehicle = Vehicle::find($request->get('vehicle_id'));
            $gpsVehicle = $vehicle->gpsVehicle;
            if( !$gpsVehicle ){
                $gpsVehicle = new GpsVehicle();
                $gpsVehicle->vehicle_id = $vehicle->id;
            }

            $created = false;
            try {
                $checkGPS = SimGPS::where('sim', $sim)->get()->first();
                $checkImei = GpsVehicle::where('imei', $imei)->where('imei', '<>', $vehicle->plate)->get()->first();


                if ($checkGPS) {
                    $companyVehicleCheck = $checkGPS->vehicle->company->short_name;
                    $message = __('The SIM number :sim is already associated with another GPS (Vehicle :vehicle)', ['sim' => $sim, 'vehicle' => $checkGPS->vehicle->number ?? 'NONE'])." ($companyVehicleCheck)";
                } elseif ($checkImei) {
                    $companyVehicleCheck = $checkImei->vehicle->company->short_name;
                    $message = __('The Imei number :imei is already associated to vehicle :vehicle', ['imei' => $imei, 'vehicle' => $checkImei->vehicle->number ?? 'NONE'])." ($companyVehicleCheck)";
                } else {
                    $gpsVehicle->imei = $imei;

                    if ($gpsVehicle->save()) {
                        $simGPS = new SimGPS();
                        $simGPS->sim = $sim;
                        $simGPS->vehicle_id = $vehicle->id;
                        $simGPS->gps_type = $gpsType;

                        $message = __('Register created successfully');

                        $simGPS->save();
                        $created = true;
                        \DB::update("UPDATE crear_vehiculo SET imei_gps = '$gpsVehicle->imei' WHERE id_crear_vehiculo = $vehicle->id"); // TODO: temporal while migration for vehicles table is completed
                    } else {
                        $message = __('Error');
                    }
                }
            } catch (Exception $exception) {
                $message = $exception->getMessage();
            }

            return (object)[
                'created' => $created,
                'message' => $message,
            ];
        });

        return response()->json(['success' => $transaction->created, 'message' => $transaction->message]);
    }


    public function updateSIMGPS(SimGPS $simGPS, Request $request)
    {
        $transaction = \DB::transaction(function () use ($request, $simGPS) {
            $sim = $request->get('sim');
            $gpsType = $request->get('gps_type');
            $imei = $request->get('imei');

            $vehicle = $simGPS->vehicle;
            $gpsVehicle = $vehicle->gpsVehicle;
            if (!$gpsVehicle) {
                $gpsVehicle = new GpsVehicle();
                $gpsVehicle->vehicle_id = $vehicle->id;
            }

            $error = "";
            $updated = false;
            try {
                $checkGPS = SimGPS::where('id', '<>', $simGPS->id)->where('sim', $sim)->get()->first();
                $checkImei = GpsVehicle::where('id', '<>', $gpsVehicle->id)->where('imei', $imei)->get()->first();

                if ($checkGPS) {
                    $companyVehicleCheck = $checkGPS->vehicle->company->short_name;
                    $error = __('The SIM number :sim is already associated with another GPS (Vehicle :vehicle)', ['sim' => $sim, 'vehicle' => $checkGPS->vehicle->number ?? 'NONE']) . " ($companyVehicleCheck)";
                } elseif ($checkImei) {
                    $companyVehicleCheck = $checkImei->vehicle->company->short_name;
                    $error = __('The Imei number :imei is already associated to vehicle :vehicle', ['imei' => $imei, 'vehicle' => $checkImei->vehicle->number ?? 'NONE']) . " ($companyVehicleCheck)";
                } else {
                    $gpsVehicle->imei = $imei;
                    $gpsVehicle->save();

                    $simGPS->sim = $sim;
                    $simGPS->gps_type = $gpsType;
                    $simGPS->updated_at = Carbon::now();
                    $simGPS->save();

                    $updated = true;

                    \DB::update("UPDATE crear_vehiculo SET imei_gps = '$imei' WHERE id_crear_vehiculo = $vehicle->id"); // TODO: temporal while migration for vehicles table is completed
                }
            } catch (\Exception $exception) {
                $error = $exception->getMessage();
            }

            return (object)[
                'updated' => $updated,
                'error' => $error,
            ];
        });

        $simGPS = $simGPS->fresh(['vehicle']);
        $gpsVehicle = $simGPS->vehicle->gpsVehicle->fresh();
        $updated = $transaction->updated;
        $error = $transaction->error;

        return view('admin.gps.manage.gpsVehicleDetail', compact(['simGPS', 'updated', 'error', 'gpsVehicle']));
    }

    public function deleteSIMGPS(SimGPS $simGPS, Request $request)
    {
        $deleted = false;
        try {
            $vehicle = $simGPS->vehicle;
            $gpsVehicle = $vehicle->gpsVehicle;
            $deleted = ($simGPS->delete() > 0);
            if( $gpsVehicle ){
                $deleted = ($gpsVehicle->delete() > 0);
            }
            if ($deleted) $message = __('Register deleted successfully');
            else $message = __('Error');
        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }
        return response()->json(['success' => $deleted, 'message' => $message]);
    }

    public function getScript($device)
    {
        switch ($device) {
            case 'general-skypatrol':
                $fileScript = 'ScriptSkypatrol.txt';
                break;
            case 'apn-skypatrol':
                $fileScript = 'ScriptAPNSkypatrol.txt';
                break;
            case 'plate-skypatrol':
                $fileScript = 'ScriptPlateSkypatrol.txt';
                break;
            case 'new-skypatrol':
                $fileScript = 'NewScriptSkypatrol.txt';
                break;
            case 'ip-skypatrol':
                $fileScript = 'ScriptIPSkypatrol.txt';
                break;
            case 'apn-claro-coban':
                $fileScript = 'ApnClaroScriptCoban.txt';
                break;
            case 'apn-movistar-coban':
                $fileScript = 'ApnMovistarScriptCoban.txt';
                break;
            case 'apn-avantel-coban':
                $fileScript = 'ApnAvantelScriptCoban.txt';
                break;
            case 'ip-ruptela':
                $fileScript = 'IpScriptRuptela.txt';
                break;
            case 'time-report-ruptela':
                $fileScript = 'TimeReportScriptRuptela.txt';
                break;
            case 'connection-report-ruptela':
                $fileScript = 'ConnectionScriptRuptela.txt';
                break;
            default:
                $fileScript = '';
                break;
        }
        if (!Storage::exists($fileScript)) Storage::put($fileScript, '');
        try {
            $script = Storage::get($fileScript);
        } catch (FileNotFoundException $e) {
            $script = null;
        }
        return $script;
    }
}
