<?php

namespace App\Http\Controllers;

use App\Company;
use App\CurrentLocationsGPS;
use App\GpsVehicle;
use App\Http\Controllers\API\SMS;
use App\SimGPS;
use App\Vehicle;
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
        if (Auth::user()->isAdmin() || Auth::user()->id == 999459 || Auth::user()->id == 841403) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('admin.gps.manage.index', compact('companies'));
    }

    public function list(Request $request)
    {
        $companyReport = $request->get('company-report');
        $defaultSelection = $request->get('default-selection');

        $simGPSList = null;
        if ($companyReport != 'any') {
            $company = (Auth::user()->isAdmin() || Auth::user()->id == 999459 || Auth::user()->id == 841403) ? Company::find($companyReport) : Auth::user()->company;
            $vehiclesCompany = $company->vehicles;
            $simGPSList = SimGPS::whereIn('vehicle_id', $vehiclesCompany->pluck('id'))->get();

            $simGPSList = $simGPSList->sortBy(function ($simGPS) {
                return $simGPS->vehicle->number ?? true;
            });

            $unAssignedVehicles = $vehiclesCompany->where('active', true)->whereNotIn('id', $simGPSList->pluck('vehicle_id'))->sortBy(function ($vehicle) {
                return $vehicle->number;
            });

            $readyVehicles = [
                //6,
                17,
                21,
                22,
                24,
                25,
                27,
                29,
                40,
                42,
                46,
                47,
                49,
                50,
                51,
                61,
                66,
                67,
                //70,
                //77,
                83,
                84,
                93,
                100,
                101,
                110,
                111,
                113,
                //114,
                125,
                132,
                // On May 3rd
                1,
                2,
                7,
                8,
                10,
                18,
                23,
                28,
                30,
                32,
                34,
                37,
                38,
                41,
                43,
                48,
                54,
                56,
                59,
                //62,
                64,
                87,
                90,
                116,
                122,
                135,
                // On May 8th
                71,
                // On may 21th
                4,
                6,
                62,
                70,
                77,
                85,
                114,
                33,
            ];
            switch ($defaultSelection) {
                case 'all':
                    foreach ($simGPSList as $simGPS) {
                        $selection[] = $simGPS->vehicle->number;
                    }
                    break;
                case 'ready':
                    $selection = $readyVehicles;
                    break;
                case 'unready':
                    foreach ($simGPSList as $simGPS) {
                        if (!in_array($simGPS->vehicle->number, $readyVehicles)) $selection[] = $simGPS->vehicle->number;
                    }
                    break;
                default:
                    $selection = [];
                    break;
            }
        }

        return view('admin.gps.manage.list', compact(['simGPSList', 'vehiclesCompany', 'selection', 'unAssignedVehicles']));
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
            $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicle->id);
            $vehicleStatus = $currentLocationGPS->vehicleStatus;
            $timePeriod = $currentLocationGPS->getTimePeriod();

            $classStatus = null;
            if ($vehicleStatus->id == 6 && ($timePeriod >= "00:04:00" && $timePeriod <= "00:06:00")) $classStatus = "btn btn-lime btn-xs";
            elseif (($timePeriod >= "00:00:13" && $timePeriod <= "00:00:17")) $classStatus = "btn btn-lime btn-xs";

            $statusList .= $vehicleStatus ? "<div class='row' style='height: 20px'><div class='col-md-1 col-sm-1 col-xs-1 text-right'><small class='$classStatus hide'>$index</small></div><div class='col-md-10 col-sm-10 col-xs-10'><span class='tooltips click' title='$vehicleStatus->des_status' data-placement='right'><i class='text-$vehicleStatus->main_class $vehicleStatus->icon_class' style='width: 15px'></i><span style='width: 20px'>$vehicle->number</span> $currentLocationGPS->date (<span class='$classStatus'>$timePeriod</span>)<br></span></div></div>" : "********";

            $reportsStatus->push((object)[
                'statusId' => $vehicleStatus->id,
                'status' => $vehicleStatus
            ]);

            if ($classStatus) $totalOKPeriod++;
            $index++;
        }

        $statusList = "<div class='text-center'><span class='btn btn-lime'>Total OK $totalOKPeriod</span></div>$statusList";

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
        foreach ($simGPSNumbers as $simGPS) {
            $dump = "************** $now >> $simGPS **************\n";
            $commands = $request->get('command-gps');
            $gpsCommands = explode("\n", $commands);

            switch ($request->get('gps-type')) {
                case 'SKYPATROL':
                    $totalCMD = "";
                    $smsCommands = [];

                    if ($commands != 'AT$RESET') {
                        $flagStartCMD = true;
                        foreach ($gpsCommands as $gpsCommand) {
                            $individualCommand = trim(explode("'", $gpsCommand)[0]);
                            if ($individualCommand) {
                                $individualCommand = str_replace(["AT", "At", "aT", "at"], "", $individualCommand);
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
                        $smsCommands[] = str_start($totalCMD, "AT") . "&W";
                        $gpsCommands = $smsCommands;
                    }
                    break;
                case 'COBAN':
                    break;
            }

            $totalSent = 0;
            foreach ($gpsCommands as $smsCommand) {
                $smsCommand = trim($smsCommand);
                $totalSent++;
                $responseSMS = SMS::sendCommand($smsCommand, $simGPS);
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
        $created = false;
        try {
            $sim = $request->get('sim');
            $gpsType = $request->get('gps_type');
            $vehicleId = $request->get('vehicle_id');

            $checkGPS = SimGPS::where('sim', $sim)->get()->first();
            $checkVehicle = SimGPS::where('vehicle_id', $vehicleId)->get()->first();
            if ($checkGPS) {
                $message = __('The SIM number :sim is already associated with another GPS (Vehicle :vehicle)', ['sim' => $sim, 'vehicle' => $checkGPS->vehicle->number ?? 'NONE']);
            } elseif ($checkVehicle) {
                $message = __('A record for this vehicle already exists');
            } else {
                $simGPS = new SimGPS();
                $simGPS->sim = $sim;
                $simGPS->vehicle_id = $vehicleId;
                $simGPS->gps_type = $gpsType;
                $simGPS->operator = starts_with($sim, '350') ? 'avantel' : 'movistar';
                if ($simGPS->save()) {
                    $created = true;
                    $message = __('Register created successfully');
                } else {
                    $message = __('Error');
                }
            }
        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        return response()->json(['success' => $created, 'message' => $message]);
    }


    public function updateSIMGPS(SimGPS $simGPS, Request $request)
    {
        try {
            $sim = $request->get('sim');
            $gpsType = $request->get('gps_type');

            $checkGPS = SimGPS::where('id', '<>', $simGPS->id)->where('sim', $sim)->get()->first();
            if ($checkGPS) {
                $error = __('The SIM number :sim is already associated with another GPS (Vehicle :vehicle)', ['sim' => $sim, 'vehicle' => $checkGPS->vehicle->number ?? 'NONE']);
            } else {
                $simGPS->sim = $sim;
                $simGPS->gps_type = $gpsType;
                $simGPS->operator = starts_with($sim, '350') ? 'avantel' : 'movistar';
                $simGPS->save();
                $updated = true;
            }
        } catch (Exception $exception) {
            $error = $exception->getMessage();
        }

        return view('admin.gps.manage.gpsVehicleDetail', compact(['simGPS', 'updated', 'error']));
    }

    public function deleteSIMGPS(SimGPS $simGPS, Request $request)
    {
        $deleted = false;
        try {
            $deleted = ($simGPS->delete() > 0);
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
            case 'ip-skypatrol':
                $fileScript = 'ScriptIPSkypatrol.txt';
                break;
            case 'coban':
                $fileScript = 'ScriptCoban.txt';
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
