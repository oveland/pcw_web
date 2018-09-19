<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Http\Controllers\Utils\StrTime;
use App\Route;
use App\Traits\CounterByRecorder;
use App\Vehicle;
use Auth;
use Illuminate\Http\Request;

class RecorderPassengerReportByFringesController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.passengers.recorders.fringes.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $companyReport = $request->get('company-report');
        $dateReport = $request->get('date-report');
        $routeReport = $request->get('route-report');

        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;

        $dispatchRegisters = DispatchRegister::where('date', $dateReport)->active();
        if( $routeReport == 'all' ){
            dd('Seleccione una ruta');
            $dispatchRegisters = $dispatchRegisters->whereIn('route_id', $company->routes->pluck('id'));
        }
        else $dispatchRegisters = $dispatchRegisters->where('route_id', $routeReport);
        $dispatchRegisters = $dispatchRegisters->orderBy('departure_time')->get();

        $dispatchRegistersByVehicles = $dispatchRegisters->sortBy(function($dispatchRegister,$routeId){
            return $dispatchRegister->vehicle->number;
        })->groupBy('vehicle_id');

        switch ($request->get('group-by')){
            case 'fringes':
                $route = Route::find($routeReport);
                $fringes = $route->fringes($dispatchRegisters->first()->type_of_day ?? 0);
                return view('reports.passengers.recorders.fringes.fringes',compact(['dispatchRegistersByVehicles', 'fringes']));
                break;
            case 'fringes-merged':
                $route = Route::find($routeReport);
                $fringes = $route->fringes($dispatchRegisters->first()->type_of_day ?? 0);
                return view('reports.passengers.recorders.fringes.fringesMerged',compact(['dispatchRegistersByVehicles', 'fringes']));
                break;
            case 'round_trips':
                return view('reports.passengers.recorders.fringes.round-trips',compact('dispatchRegistersByVehicles'));
                break;
            default:
                return null;
                break;
        }
    }
}
