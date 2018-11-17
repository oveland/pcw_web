<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
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
        return view('admin.gps.manage.index', compact('companies'));
    }

    public function list(Request $request)
    {
        $companyReport = $request->get('company-report');
        $gpsReport = $request->get('gps-report');
        $optionSelection = $request->get('option-selection');

        $simGPSList = null;
        if ($companyReport != 'any') {
            $company = (Auth::user()->isAdmin()) ? Company::find($companyReport) : Auth::user()->company;
            $vehiclesCompany = $company->vehicles;
            $simGPSList = SimGPS::whereIn('vehicle_id', $vehiclesCompany->pluck('id'))
                ->where('gps_type', $gpsReport)
                ->get();

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
                        $selection[] = $simGPS->vehicle->number;
                    }
                    break;
                case 'no-report':
                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id);
                        $vehicleStatus = $currentLocationGPS->vehicleStatus;
                        if ($vehicleStatus->id == VehicleStatus::NO_REPORT) $selection[] = $simGPS->vehicle->number;
                    }
                    break;
                case 'without-gps-signal':
                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id);
                        $vehicleStatus = $currentLocationGPS->vehicleStatus;
                        if ($vehicleStatus->id == VehicleStatus::WITHOUT_GPS_SIGNAL) $selection[] = $simGPS->vehicle->number;
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
        $totalOKPeriod = 0;
        $index = 1;
        foreach ($simGPSList as $sim) {
            $simGPS = SimGPS::where('sim', $sim)->get()->first();
            $vehicle = $simGPS->vehicle;
            $currentLocationGPS = CurrentLocationsGPS::where('vehicle_id', $vehicle->id)->get()->first() ?? null;

            $classStatus = null;
            if($currentLocationGPS){
                $vehicleStatus = $currentLocationGPS->vehicleStatus;
                $timePeriod = $currentLocationGPS->getTimePeriod();

                if ($vehicleStatus->id == 6 && ($timePeriod >= "00:04:00" && $timePeriod <= "00:06:00")) $classStatus = "btn btn-lime btn-xs";
                elseif (($timePeriod >= "00:00:13" && $timePeriod <= "00:00:17")) $classStatus = "btn btn-lime btn-xs";

                $statusList .= $vehicleStatus ? "<div class='row' style='height: 20px'><div class='col-md-1 col-sm-1 col-xs-1 text-right'><small class='$classStatus hide'>$index</small></div><div class='col-md-10 col-sm-10 col-xs-10'><span class='tooltips click' title='$vehicleStatus->des_status' data-placement='right'><i class='text-$vehicleStatus->main_class $vehicleStatus->icon_class' style='width: 15px'></i><span style='width: 20px'>$vehicle->number</span> $currentLocationGPS->date (<span class='$classStatus'>$timePeriod</span>)<br></span></div></div>" : "********";

                $reportsStatus->push((object)[
                    'statusId' => $vehicleStatus->id,
                    'status' => $vehicleStatus
                ]);
            }


            if ($classStatus) $totalOKPeriod++;
            $index++;
        }

        $statusList = "<div class='text-center'><span class='btn btn-xs btn-lime'>Período OK » $totalOKPeriod</span></div>$statusList";
        $headerReport = "";
        $reportsByStatus = $reportsStatus->sortBy('statusId')->groupBy('statusId');
        foreach ($reportsByStatus as $statusId => $reportStatus) {
            if ($reportStatus->first()) {
                $status = $reportStatus->first()->status;
                $headerReport .= "<span class='tooltips click btn btn-$status->main_class btn-sm' style='border-radius: 0' title='$status->des_status' data-placement='bottom'><i class='text-white $status->icon_class' style='width: 15px'></i> <strong>" . count($reportStatus) . "</strong></span>";
            }
        }

        return "<div class='text-center'>Total: " . count($simGPSList) . "<br>$headerReport</div> <hr class='m-t-5 m-b-5'>$statusList";
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
                case SimGPS::SKYPATROL:
                    $totalCMD = "";
                    $smsCommands = [];

                    if ($commands != 'AT$RESET') {
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
                                        $smsCommands[] = str_start($totalCMD, "AT") . "&W";
                                        $totalCMD = $individualCommand . ";";
                                        $flagStartCMD = true;
                                    }
                                }
                            }
                        }
                        $smsCommands[] = str_start($totalCMD, "AT") . "&W";

                        $gpsCommands = $smsCommands;
                    }
                    break;
                case SimGPS::COBAN:
                    break;
            }

            $totalSent = 0;
            foreach ($gpsCommands as $smsCommand) {
                $smsCommand = trim($smsCommand);
                $totalSent++;
                $responseSMS = SMS::sendCommand($smsCommand, $sim);
                //$responseSMS = ['resultado' => 1];
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
                $checkImei = GpsVehicle::where('imei', $imei)->get()->first();

                if ($checkGPS) {
                    $companyVehicleCheck = $checkGPS->vehicle->company->short_name;
                    $message = __('The SIM number :sim is already associated with another GPS (Vehicle :vehicle)', ['sim' => $sim, 'vehicle' => $checkGPS->vehicle->number ?? 'NONE'])." ($companyVehicleCheck)";
                } elseif ($checkImei) {
                    $companyVehicleCheck = $checkImei->vehicle->company->short_name;
                    $message = __('The Imei number :imei is already associated to vehicle :vehicle', ['imei' => $imei, 'vehicle' => $checkImei->vehicle->number ?? 'NONE'])." ($companyVehicleCheck)";
                } else {
                    $simGPS = new SimGPS();
                    $simGPS->sim = $sim;
                    $simGPS->vehicle_id = $vehicle->id;
                    $simGPS->gps_type = $gpsType;

                    if ($simGPS->save()) {
                        $message = __('Register created successfully');

                        $gpsVehicle->imei = $imei;
                        $gpsVehicle->save();
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
                    $simGPS->sim = $sim;
                    $simGPS->gps_type = $gpsType;
                    $simGPS->save();

                    $gpsVehicle->imei = $imei;
                    $gpsVehicle->save();
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
            case 'ruptela':
                $fileScript = 'ScriptRuptela.txt';
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
