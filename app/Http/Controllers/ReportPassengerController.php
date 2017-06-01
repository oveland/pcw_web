<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\HistorySeat;
use App\Vehicle;
use Illuminate\Http\Request;

class ReportPassengerController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $companies = Company::where('estado', '=', true)->orderBy('des_corta','asc')->get();

        return view('passengers.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $vehiclesForCompany = Vehicle::where('empresa','=',$request->get('company-report'))->where('estado','=',1)->get()->pluck('placa');
        $historySeats = HistorySeat::whereIn('plate',$vehiclesForCompany)->where('date','=',$request->get('date-report'))->get()->sortBy('active_time');

        return view('passengers.passengersReport', compact('historySeats'));
    }
}
