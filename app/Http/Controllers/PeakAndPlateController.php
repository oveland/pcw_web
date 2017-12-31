<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\Utils\Url;
use App\PeakAndPlate;
use App\Vehicle;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PeakAndPlateController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin())abort(403);
        $companies = Company::active()->orderBy('short_name')->get();
        return view('admin.vehicles.peak-and-plate.index', compact('companies'));
    }

    public function show(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehicles = $company->vehicles->where('active', true)->sortBy(function($vehicle,$key){
            return substr($vehicle->plate,-1);
        });

        $peakAndPlateReports = collect([]);
        foreach ($vehicles as $vehicle) {
            $peakAndPlate = PeakAndPlate::where('vehicle_id', $vehicle->id)->get()->first();
            $peakAndPlateReports->push((object)[
                'vehicleId' => $vehicle->id,
                'vehicleNumber' => $vehicle->number,
                'vehiclePlate' => $vehicle->plate,
                'assigned' => $peakAndPlate ? true : false,
                'weekDay' => $peakAndPlate->week_day ?? 0,
                'date' => $peakAndPlate ? ($peakAndPlate->date ? $peakAndPlate->date->toDateString() : null) : null,
            ]);
        }

        return view('admin.vehicles.peak-and-plate.show', compact('peakAndPlateReports'));
    }

    public function update(Request $request){
        $vehicle = Vehicle::findOrFail( $request->get('vehicleId') );
        $date = Carbon::createFromFormat('Y-m-d',$request->get('date'));

        $peakAndPlate = PeakAndPlate::where('vehicle_id', $vehicle->id)->get()->first();
        if( !$peakAndPlate )$peakAndPlate = new PeakAndPlate();
        $peakAndPlate->date = $date->toDateString();
        $peakAndPlate->vehicle_id = $vehicle->id;
        $peakAndPlate->week_day = $date->dayOfWeek;

        $peakAndPlate->save();

        return $vehicle->plate.' -> '.$date->dayOfWeek;
    }

    public function reset(Request $request){
        $company = Auth::user()->isAdmin() ? Company::find($request->get('companyId')) : Auth::user()->company;

        $response = \DB::delete("DELETE FROM peak_and_plates WHERE vehicle_id IN (SELECT id FROM vehicles WHERE company_id = $company->id)");

        return $response;
    }
}
