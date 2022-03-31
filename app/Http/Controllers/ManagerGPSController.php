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
use Illuminate\Support\Facades\DB;
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
        $activeVehicles = $request->get('active-vehicles');
        $excludeInRepair = $request->get('exclude-in-repair');
        $isInLimbo = $request->get('limbo') == "si" ? true : false;

        $dispatcherVehicle = $routeReport != 'all' ? DispatcherVehicle::where('route_id', $routeReport)->get() : collect([]);

        $simGPSList = null;
        if ($companyReport != 'any') {
            $company = (Auth::user()->isAdmin()) ? Company::find($companyReport) : Auth::user()->company;

            if ($activeVehicles) $vehiclesCompany = $company->activeVehicles;
            else $vehiclesCompany = $company->vehicles;

            if ($excludeInRepair) $vehiclesCompany = $vehiclesCompany->where('in_repair', false);

            $simGPSList = SimGPS::whereIn('vehicle_id', $vehiclesCompany->pluck('id'));

            $simGPSList = ($gpsReport != 'all') ? $simGPSList->where('gps_type', $gpsReport)->get() : $simGPSList->get();

            if ($isInLimbo) {
                $gpsLimbo = collect(['7', '9', '10', '23', '24', '2000', '2038', '2066', '2070', '2182', '2342', '2346', '2347', '2349', '2406', '2420', '2427', '2430', '2448', '2477', '2484', '4402', '4455', '4456', '4466', '4483', '4484', '4486', '4492', '4516', '4559']);
                $simGPSList = $simGPSList->filter(function ($simGPS) use ($gpsLimbo) {
                    return $gpsLimbo->contains($simGPS->vehicle->number);
                });
            }

            $simGPSList = $simGPSList->sortBy(function ($simGPS) {
                return $simGPS->vehicle->number ?? true;
            });


            $unAssignedVehicles = $vehiclesCompany
                ->whereNotIn('id', SimGPS::whereIn('vehicle_id', $vehiclesCompany->pluck('id'))->get()->pluck('vehicle_id'))
                ->sortBy(function ($vehicle) {
                    return $vehicle->number;
                });

            $selection = array();
            switch ($optionSelection) {
                case 'all':
                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        if ($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count()) $selection[] = $simGPS->vehicle->number;
                    }
                    break;
                case 'no-report':

                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id)->get()->first();
                        if ($currentLocationGPS) {
                            try {
                                $vehicleStatus = $currentLocationGPS->vehicleStatus;
                            } catch (\Exception $exception) {
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
                        if ($currentLocationGPS) {
                            $vehicleStatus = $currentLocationGPS->vehicleStatus;
                            if ($vehicleStatus->id != VehicleStatus::NO_REPORT) $selection[] = $simGPS->vehicle->number;
                        }
                    }
                    break;
                case 'power-off':

                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id)->get()->first();
                        if ($currentLocationGPS) {
                            try {
                                $vehicleStatus = $currentLocationGPS->vehicleStatus;
                            } catch (\Exception $exception) {
                                dd($currentLocationGPS);
                            }
                            if ($vehicleStatus->id == VehicleStatus::POWER_OFF && ($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count())) $selection[] = $simGPS->vehicle->number;
                        }
                    }
                    break;
                case 'parked':

                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id)->get()->first();
                        if ($currentLocationGPS) {
                            try {
                                $vehicleStatus = $currentLocationGPS->vehicleStatus;
                            } catch (\Exception $exception) {
                                dd($currentLocationGPS);
                            }
                            if ($vehicleStatus->id == VehicleStatus::PARKED && ($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count())) $selection[] = $simGPS->vehicle->number;
                        }
                    }
                    break;
                case 'parked':

                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id)->get()->first();
                        if ($currentLocationGPS) {
                            try {
                                $vehicleStatus = $currentLocationGPS->vehicleStatus;
                            } catch (\Exception $exception) {
                                dd($currentLocationGPS);
                            }
                            if ($vehicleStatus->id == VehicleStatus::WITHOUT_GPS_SIGNAL && ($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count())) $selection[] = $simGPS->vehicle->number;
                        }
                    }
                    break;
                case 'new':
                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        if (!$vehicle || !$vehicle->currentLocation && ($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count())) $selection[] = $simGPS->vehicle->number;
                    }
                    break;
                case 'pending-to-20-s':
                    $allPending = "
                    7
                    10
                    1146
                    2000
                    2009
                    2020
                    2038
                    2063
                    2066
                    2070
                    2182
                    2356
                    2406
                    2427
                    2428
                    2439
                    2442
                    2442
                    2444
                    2445
                    2457
                    2484
                    4088
                    4090
                    4091
                    4210
                    4402
                    4455
                    4466
                    4483
                    4485
                    4486
                    4491
                    4492
                    4512
                    4514
                    4526
                    4528
                    4534
                    4538
                    4561
                    4562
                    7007
                    1
                    2
                    17
                    19
                    20
                    23
                    24
                    27
                    2014
                    2062
                    2174
                    2337
                    2434
                    2437
                    4404
                    4544
                    2049
                    4477
                    7014
                    7015
                    2473
                    2387
                    ";

                    $allPendingArray = explode(",", str_replace("\n", ",", str_replace(" ", "", trim($allPending))));

                    $simGPSList = $simGPSList->filter(function ($sg) use ($allPendingArray) {
                        return in_array($sg->vehicle->number, $allPendingArray);
                    });

                    foreach ($simGPSList as $simGPS) {
                        $vehicle = $simGPS->vehicle;
                        if ($routeReport == 'all' || $dispatcherVehicle->where('vehicle_id', $vehicle->id)->count()) {
                            $selection[] = $simGPS->vehicle->number;
                        }
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
        $totalFrequencyOK = 0;

        foreach ($simGPSList as $sim) {
            $simGPS = SimGPS::where('sim', $sim)->get()->first();
            $vehicle = $simGPS->vehicle;
            $gpsVehicle = GpsVehicle::where('vehicle_id', $vehicle->id)->get()->first();

            $currentLocationGPS = CurrentLocationsGPS::where('vehicle_id', $vehicle->id)->get()->first() ?? null;

            //dd($currentLocationGPS);

            $classStatus = "btn btn-xs btn-default p-2 text-bold";
            if ($currentLocationGPS) {
                $vehicleStatus = $currentLocationGPS->vehicleStatus;
                $timePeriod = $currentLocationGPS->getTimePeriod();
                if ($vehicleStatus->id == VehicleStatus::OK || $vehicleStatus->id == VehicleStatus::PARKED || $vehicleStatus->id == VehicleStatus::POWER_OFF
                    || $vehicleStatus->id == VehicleStatus::WITHOUT_GPS_SIGNAL) {
                    if ($timePeriod >= '00:00:00' && $timePeriod <= "00:02:25") {
                        $classStatus .= " btn-success text-white";
                        $totalFrequencyOK++;
                    } else {
                        $classStatus .= " text-info";
                    }
                    $totalOK++;
                } else {
                    $classStatus .= " text-gray";
                }

                $imei = "";
                if (Auth::user()->isSuperAdmin() && $gpsVehicle) {
                    $imei = "(<b>$gpsVehicle->imei</b> • $simGPS->sim)";
                }

                $statusList .= $vehicleStatus ? "<div style='margin: 0;border-top: 1px solid lightgrey !important;padding: 5px'><a href='tel:$simGPS->sim' class='tooltips click' title='$vehicleStatus->des_status' data-placement='left'><i class='text-$vehicleStatus->main_class $vehicleStatus->icon_class' style='width: 15px'></i> <span class='' style='width: 20px; border-radius: 5px'>$vehicle->number</span> $imei > $currentLocationGPS->date • <small class='$classStatus'>$timePeriod</small></a></div>" : "********";

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

        return "<div class='text-center'>GPS OK $totalOK of " . count($simGPSList) . " in total<br>$totalFrequencyOK frequency OK<br>$headerReport</div> <hr class='m-t-5 m-b-5'>$statusList";
    }

    public function sendSMS(Request $request)
    {
        $gpsType = $request->get('gps-type');
        if (!Auth::user()->canSendSMS()) dd('Por temas administrativos la acción requerida se ha deshabilitado temporalmente');

        $simGPSList = $request->get('sim-gps');

        $simGPSNumbers = is_array($simGPSList) ? $simGPSList : explode(";", $simGPSList);
        $now = Carbon::now();
        foreach ($simGPSNumbers as $sim) {
            $dump = "************** $now >> $sim **************\n";
            $commands = $request->get('command-gps');
            $gpsCommands = explode("\n", $commands);

            switch ($gpsType) {
                case SimGPS::SKYPATROL_OLD:
                    $smsCommands = [];

                    foreach ($gpsCommands as $gpsCommand) {
                        $gpsCommand = explode(";", $gpsCommand);
                        $simGPS = SimGPS::findBySim($sim);
                        if ($simGPS && $simGPS->gps) {
                            foreach ($gpsCommand as &$c) {
                                if (str_contains($c, "ID=")) {
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

                    if ($commands != 'AT&W' && $commands != 'AT&F' && $commands != 'AT$RESET') {
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
                                            $gpsVehicle = GpsVehicle::findByVehicleId($vehicle->id);

                                            $gpsId = $vehicle->plate;
                                            if ($gpsVehicle) {
                                                $gpsId = $gpsVehicle->imei;
                                            }

                                            $individualCommand = '$TTDEVID="' . $gpsId . '"';
                                            dump(" - Auto set GPS Id as: $gpsId");
                                        }
                                    } else {
                                        dd("Error: Está intentando establecer una placa que no existe en la configuración de SIM-GPS: $individualCommand");
                                    }

                                    if (!$request->get('auto-set-plate') && count($simGPSNumbers) > 1) {
                                        dd("Error: No es posible establecer la misma placa para varios vehículos. Seleccione la opción 'Auto setear placa'");
                                    }
                                }

                                if (str_contains($individualCommand, "TTSTOCMD=12")) $individualCommand = 'AT$TTSTOCMD=12,AT$RESET';

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
                        if (!starts_with($gpsCommand, ' ')) {
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

                $dump .= ("$smsCommand \n $length Chars (" . ($responseSMS['status'] === '1x000' ? "successfully" : $responseSMS['error_description']) . ")") . "\n\n";
                sleep(1);
            }
            $dump .= "-------------- TOTAL SMS SENT: $totalSent --------------\n";
            dump($dump);
        }

        dd("................................................");
    }

    public function createSIMGPS(Request $request)
    {
        $transaction = \DB::transaction(function () use ($request) {
            $sim = $request->get('sim');
            $gpsType = $request->get('gps_type');
            $imei = $request->get('imei');

            $vehicle = Vehicle::find($request->get('vehicle_id'));
            $gpsVehicle = $vehicle->gpsVehicle;
            if (!$gpsVehicle) {
                $gpsVehicle = new GpsVehicle();
                $gpsVehicle->vehicle_id = $vehicle->id;
            }

            $created = false;
            try {
                $message = "";

                $checkGPS = SimGPS::where('sim', $sim)->get()->first();
                $checkImei = GpsVehicle::where('imei', $imei)->where('imei', '<>', $vehicle->plate)->get()->first();

                if ($checkGPS) {
                    $companyVehicleCheck = $checkGPS->vehicle->company->short_name;
                    $message .= __('The SIM number :sim is already associated with another GPS (Vehicle :vehicle)', ['sim' => $sim, 'vehicle' => $checkGPS->vehicle->number ?? 'NONE']) . " ($companyVehicleCheck) <br><br>";
                }

                if($checkImei) {
                    $companyVehicleCheck = $checkImei->vehicle ? $checkImei->vehicle->company->short_name : "";
                    $message .= __('The Imei number :imei is already associated to vehicle :vehicle', ['imei' => $imei, 'vehicle' => $checkImei->vehicle->number ?? 'NONE']) . " ($companyVehicleCheck) <br><br>";
                }

                if($checkGPS || $checkImei) {
//                    dump($imei, $sim);
                    $this->deleteCurrent($imei, $sim);
                    $message .= "<br> • Se ha eliminado registro anterior<br><br>";
                }

                $gpsVehicle->imei = $imei;

                if ($gpsVehicle->save()) {
                    $simGPS = new SimGPS();
                    $simGPS->sim = $sim;
                    $simGPS->vehicle_id = $vehicle->id;
                    $simGPS->gps_type = $gpsType;

                    $message .= __('Register created successfully');

                    $simGPS->save();
                    $created = true;
                    \DB::update("UPDATE crear_vehiculo SET imei_gps = '$gpsVehicle->imei' WHERE id_crear_vehiculo = $vehicle->id"); // TODO: temporal while migration for vehicles table is completed

                    DB::commit();
                } else {
                    $message .= __('Error');
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

//return response()->json(['message'=>'Acceso restringido']);

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

            $updated = false;
            $error = "";

            try {
                $message = "";

                $checkGPS = SimGPS::where('id', '<>', $simGPS->id)->where('sim', $sim)->get()->first();
                $checkImei = GpsVehicle::where('id', '<>', $gpsVehicle->id)->where('imei', $imei)->get()->first();

                if ($checkGPS) {
                    $companyVehicleCheck = $checkGPS->vehicle ? $checkGPS->vehicle->company->short_name : "SimGPS with ID $checkGPS->id";
                    $message .= __('The SIM number :sim is already associated with another GPS (Vehicle :vehicle)', ['sim' => $sim, 'vehicle' => $checkGPS->vehicle->number ?? 'NONE']) . " ($companyVehicleCheck) <br><br>";
                    $checkGPS->delete();
                    $message .= "<br> • Se ha eliminado registro anterior<br><br>";
                }

                if ($checkImei) {
                    $companyVehicleCheck = $checkImei->vehicle->company->short_name;
                    $message .= __('The Imei number :imei is already associated to vehicle :vehicle', ['imei' => $imei, 'vehicle' => $checkImei->vehicle->number ?? 'NONE']) . " ($companyVehicleCheck) <br><br>";
                    $checkImei->delete();
                    $message .= "<br> • Se ha eliminado registro anterior<br><br>";
                }


                $gpsVehicle->imei = $imei;
                $gpsVehicle->save();

                $simGPS->sim = $sim;
                $simGPS->gps_type = $gpsType;
                $simGPS->updated_at = Carbon::now();
                $simGPS->save();

                $updated = true;
                $message .= "Actualizado correctamente";

                \DB::update("UPDATE crear_vehiculo SET imei_gps = '$imei' WHERE id_crear_vehiculo = $vehicle->id"); // TODO: temporal while migration for vehicles table is completed
            } catch (\Exception $exception) {
                $error = $exception->getMessage();
                DB::rollBack();
            }

            return (object)[
                'updated' => $updated,
                'error' => $error,
                'message' => $message,
            ];
        });

        $simGPS = $simGPS->fresh(['vehicle']);
        $gpsVehicle = $simGPS->vehicle->gpsVehicle->fresh();
        $updated = $transaction->updated;
        $error = $transaction->error;
        $message = $transaction->message;

        return view('admin.gps.manage.gpsVehicleDetail', compact(['simGPS', 'updated', 'error', 'message', 'gpsVehicle']));
    }

    public function deleteSIMGPS(SimGPS $simGPS, Request $request)
    {
        $user = Auth::user();
        if ($user->isSuperAdmin() || $user->id == 2018101054) {
            $deleted = false;
            try {
                $vehicle = $simGPS->vehicle;
                $gpsVehicle = $vehicle->gpsVehicle;
                $deleted = ($simGPS->delete() > 0);
                if ($gpsVehicle) {
                    $deleted = ($gpsVehicle->delete() > 0);
                }
                if ($deleted) $message = __('Register deleted successfully');
                else $message = __('Error');
            } catch (Exception $exception) {
                $message = $exception->getMessage();
            }
            return response()->json(['success' => $deleted, 'message' => $message]);
        } else {
            return response()->json(['message' => 'Acceso restringido']);
        }
    }

    public function deleteCurrent($imei, $sim)
    {
        $simGPSList = SimGPS::where('sim', $sim)->get();
        $gpsVehicleList = GpsVehicle::where('imei', $imei)->get();

        $simGPSList->each(function (SimGPS $simGPS) {
            $simGPS->delete();
        });

        $gpsVehicleList->each(function (GpsVehicle $gpsVehicle) {
            $gpsVehicle->delete();
        });

    }

    public function getScript($device)
    {
        switch ($device) {
            case 'general-skypatrol-8750+':
                $fileScript = 'ScriptSkypatrol.txt';
                break;
            case 'apn-skypatrol-8750+':
                $fileScript = 'ScriptAPNSkypatrol.txt';
                break;
            case 'plate-skypatrol-8750+':
                $fileScript = 'ScriptPlateSkypatrol.txt';
                break;
            case 'new-skypatrol-8750+':
                $fileScript = 'NewScriptSkypatrol.txt';
                break;
            case 'ip-skypatrol-8750+':
                $fileScript = 'ScriptIPSkypatrol.txt';
                break;

            case 'general-skypatrol-8750':
                $fileScript = 'ScriptSkypatrol8750.txt';
                break;
            case 'apn-skypatrol-8750':
                $fileScript = 'ScriptAPNSkypatrol8750.txt';
                break;
            case 'id-skypatrol-8750':
                $fileScript = 'ScriptPlateSkypatrol8750.txt';
                break;
            case 'new-skypatrol-8750':
                $fileScript = 'NewScriptSkypatrol8750.txt';
                break;
            case 'ip-skypatrol-8750':
                $fileScript = 'ScriptIPSkypatrol8750.txt';
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
