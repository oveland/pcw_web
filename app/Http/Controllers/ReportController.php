<?php

namespace App\Http\Controllers;

use App\Company;
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
        $companies = Company::where('estado', '=', true)->orderBy('des_corta','asc')->get();
        return view('reports.route', compact('companies'));
    }

    public function show(Request $request)
    {
        $dispatchRegisters = DispatchRegister::where('fecha', '=', $request->get('date-report'))
            ->where('id_ruta', '=', $request->get('route-report'))
            ->get();

        return view('reports.routeReport', compact('dispatchRegisters'));
    }

    public function ajax(Request $request)
    {
        switch ($request->get('option')){
            case 'loadRoutes':
                $routes = Route::where('id_empresa', '=', $request->get('company'))->orderBy('nombre','asc')->get();
                return view('reports.routeSelect', compact('routes'));
            break;
            default:
                return "Nothing to do";
            break;
        }
    }
}
