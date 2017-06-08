<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\HistorySeat;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportPassengerController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if( Auth::user()->isAdmin() ){
            $companies = Company::where('active', '=', true)->orderBy('shortName','asc')->get();
        }
        return view('passengers.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        if(Auth::user()->isAdmin()){
            $company = $request->get('company-report');
        }else{
            $company = Auth::user()->company->id;
        }
        $vehiclesForCompany = Vehicle::where('empresa','=',$company)->where('estado','=',1)->get()->pluck('placa');
        $historySeats = HistorySeat::whereIn('plate',$vehiclesForCompany)->where('date','=',$request->get('date-report'))->get()->sortBy('active_time');

        return view('passengers.passengersReport', compact('historySeats'));
    }
}
