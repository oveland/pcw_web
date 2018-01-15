<?php

namespace App\Http\Controllers;

use App\Company;
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
        $gpsVehicles = null;
        if( $companyReport != 'any' ){
            $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;
            $vehiclesCompany = $company->vehicles;
            $gpsVehicles = SimGPS::whereIn('vehicle_id',$vehiclesCompany->pluck('id'))->orderBy('gps_type')->get();
        }
        return view('admin.gps.manage.list',compact('gpsVehicles'));
    }

    public function sendSMS(Request $request)
    {
        $simGPS = $request->get('sim-gps');
        $commandsGPS = explode("\n",$request->get('command-gps'));

        $totalCMD = '';
        foreach ($commandsGPS as $commandGPS){
            $sendCommand = trim(explode("'",$commandGPS)[0]);
            if( $sendCommand ){
                $totalCMD .= $sendCommand."\n";
            }
        }
        $totalCMD = $totalCMD;
        $responseSMS = SMS::sendCommand($totalCMD,$simGPS);
        dump($responseSMS);

        dump("COMMAND SENT (".($responseSMS['resultado'] === 0 ?"successfully":"error")."):");
        dd($totalCMD);
    }
}
