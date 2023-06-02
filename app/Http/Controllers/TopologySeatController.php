<?php
namespace App\Http\Controllers;
use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Models\Vehicles\TopologiesSeats;
use App\Models\Vehicles\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class
TopologySeatController extends Controller {
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
            $vehicles = Vehicle::whereCompanyId('39')->get();
        }
        $caso= TopologiesSeats::all()->get()->first;
        //dd($caso);

        return view('admin.vehicles.topologies.index', compact('companies'),compact('vehicles'));

    }
    public function table(){
        //$resultados=  DB::table('topologies_seats')->get()->first();
        $prueba = TopologiesSeats::all();
        dd($prueba);
        return view('admin.vehicles.topologies._table',compact($prueba));

    }
}