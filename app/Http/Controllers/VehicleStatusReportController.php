<?php

namespace App\Http\Controllers;

use App\Company;
use App\VehicleStatus;
use App\Vehicle;
use App\VehicleStatusReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VehicleStatusReportController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::active()->get()->sortBy(function($v){
            return $v->company->short_name;
        });
        return view('reports.vehicles.status.index',compact('vehicles'));
    }

    public function searchReport(Request $request)
    {
        $vehicleStatusReports = VehicleStatusReport::with('status')
            ->where('date',$request->get('date-report'))
            ->whereIn('vehicle_id',$request->get('vehicles-report'))
            ->get()
            ->groupBy('vehicle_id');

        return view('reports.vehicles.status.report', compact('vehicleStatusReports'));
    }
}
