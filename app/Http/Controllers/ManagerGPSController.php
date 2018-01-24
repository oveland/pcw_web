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
use Illuminate\Http\Request;

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
        $simGPSList = null;
        if ($companyReport != 'any') {
            $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;
            $vehiclesCompany = $company->vehicles;
            $simGPSList = SimGPS::whereIn('vehicle_id', $vehiclesCompany->pluck('id'))->get();

            $simGPSList = $simGPSList->sortBy(function ($simGPS) {
                return $simGPS->vehicle->number ?? true;
            });
        }
        return view('admin.gps.manage.list', compact('simGPSList'));
    }

    public function getVehicleStatus(Request $request)
    {
        $vehicleId = $request->get('vehicleId');
        $currentLocationGPS = CurrentLocationsGPS::findByVehicleId($vehicleId);
        return $currentLocationGPS->vehicleStatus->des_status ?? 'NONE';
    }

    public function sendSMS(Request $request)
    {
        $commands = $request->get('command-gps');
        $gpsCommands = explode("\n", $commands);

        switch ( $request->get('gps-type') ){
            case 'SKYPATROL':
                $totalCMD = "";
                $smsCommands = [];

                if( $commands != 'AT$RESET' ){
                    $flagStartCMD = true;
                    foreach ($gpsCommands as $gpsCommand) {
                        $individualCommand = trim(explode("'", $gpsCommand)[0]);
                        if ($individualCommand) {
                            $individualCommand = str_replace(["AT", "At", "aT", "at"], "", $individualCommand);
                            if (strlen($totalCMD) + strlen($individualCommand) + 2 < config('sms.sms_max_length_for_gps')) {
                                $totalCMD .= ($flagStartCMD ? "" : ";") . $individualCommand;
                                $flagStartCMD = false;
                            } else {
                                $smsCommands[] = str_start($totalCMD, "AT")."&W";
                                $totalCMD = $individualCommand . ";";
                                $flagStartCMD = true;
                            }
                        }
                    }
                    $smsCommands[] = str_start($totalCMD, "AT")."&W";
                    $gpsCommands = $smsCommands;
                }
                break;
            case 'TRACKER':
                break;
        }

        $totalSent = 0;
        foreach ($gpsCommands as $smsCommand) {
            $smsCommand = trim($smsCommand);
            $totalSent++;
            $responseSMS = SMS::sendCommand($smsCommand, $simGPS);
            $length = strlen($smsCommand);
            dump("$totalSent. $smsCommand | $length Chars (" . ($responseSMS['resultado'] === 0 ? "successfully" : "error") . "):");
            sleep(1);
        }
        dd("Total Sent: $totalSent");
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
                $simGPS->save();
                $updated = true;
            }
        } catch (Exception $exception) {
            $error = $exception->getMessage();
        }

        return view('admin.gps.manage.gpsVehicleDetail', compact(['simGPS', 'updated', 'error']));
    }
}
