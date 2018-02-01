<?php

namespace App\Http\Controllers;

use App\Company;
use App\PeakAndPlate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiPeakAndPlateController extends Controller
{
    public function getVehiclesCurrentPeakAndPlate(Company $company)
    {
        $now = Carbon::now();
        $vehicles = $company->vehicles;
        $peakAndPlateList = PeakAndPlate::whereIn('vehicle_id',$vehicles->pluck('id'))->get();
        $vehiclesCurrentPeakAndPlate = collect([]);
        foreach ($peakAndPlateList as $peakAndPlate){
            $date = $peakAndPlate->date;
            if($now->toDateString() == $date->toDateString()){
                $vehiclesCurrentPeakAndPlate->push($peakAndPlate->vehicle->plate);
            }else{
                while( $date->addDays(5)->lessThan($now) ){
                    if($now->toDateString() == $date->toDateString()){
                        $vehiclesCurrentPeakAndPlate->push($peakAndPlate->vehicle->plate);
                        continue;
                    }
                }
            }
        }

        return response()->json($vehiclesCurrentPeakAndPlate);
    }
}
