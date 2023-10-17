<?php
namespace App\Http\Controllers;
use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Models\Vehicles\SimGPS;
use App\Models\Vehicles\Speeding;
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

        return view('admin.vehicles.topologies.index', compact('companies'),compact('vehicles'));


    }
    public function table(Request $request)
    {
        $company = $request->input('company-report');
        $vehicle = $request->input('vehicle-report');
        $camara = $request->input('cameras');

        if ($vehicle == 'all' && $camara == 'all') {
            $topologies = TopologiesSeats::where('company_id',$company)->with('vehicle')->orderBy('vehicle_id','desc')->get();
            return view('admin.vehicles.topologies._table', compact('topologies'));
        } elseif ($vehicle != 'all' && $camara != 'all') {
            $topologies = TopologiesSeats::query()
                ->where('vehicle_id', $vehicle)
                ->where('number_cam', $camara)
                ->with('vehicle')
                ->get();


            return view('admin.vehicles.topologies._table', compact('topologies'));
        }elseif ($vehicle != 'all' && $camara == 'all') {
            $topologies = TopologiesSeats::query()
                ->where('vehicle_id', $vehicle)
                ->with('vehicle')
                ->get();
            return view('admin.vehicles.topologies._table', compact('topologies'));
        }elseif ($vehicle == 'all' && $camara != 'all') {
            $topologies = TopologiesSeats::where('company_id',$company)->with('vehicle')->orderBy('vehicle_id','desc')->get();
            return view('admin.vehicles.topologies._table', compact('topologies'));
        }

    }
}