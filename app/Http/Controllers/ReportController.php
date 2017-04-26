<?php

namespace App\Http\Controllers;

use App\CrearVehiculo;
use App\DispatchRegister;
use App\Report;
use App\Route;
use App\Vehicle;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $routes = Route::where('id_empresa', '=', config('constants.COMPANY_REPORT'))->get();
        return view('reports.route', compact('routes'));
    }

    public function show(Request $request)
    {
        $dispatchRegisters = DispatchRegister::where('fecha', '=', $request->get('date-report'))
            ->where('id_empresa', '=', config('constants.COMPANY_REPORT'))
            ->where('id_ruta', '=', $request->get('route-report'))
            ->get();

        return view('reports.routeReport', compact('dispatchRegisters'));
    }
}
