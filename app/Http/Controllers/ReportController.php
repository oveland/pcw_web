<?php

namespace App\Http\Controllers;

use App\CrearVehiculo;
use App\DispatchRegister;
use App\Report;
use App\Vehicle;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.route');
    }

    public function show(Request $request)
    {
        $dispatchRegisters = DispatchRegister::where('fecha','=',$request->get('dateReport'))->where('id_empresa','=','14')->get();
        return view('reports.routeReport',compact('dispatchRegisters'));
    }
}
