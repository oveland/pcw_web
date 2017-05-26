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
        $roundTripDispatchRegisters = DispatchRegister::where('fecha', '=', $request->get('date-report'))
            ->where('id_ruta', '=', $request->get('route-report'))
            ->get()->groupBy('n_vuelta');

        return view('reports.routeReport', compact('roundTripDispatchRegisters'));
    }

    public function chart(DispatchRegister $dispatchRegister)
    {
        $dataReport = ['empty'=>true];
        $report = $dispatchRegister->reports->sortBy('date');

        if( $report->isNotEmpty() ){
            $routeDistance = $dispatchRegister->route->distance*1000;
            $totalDistance = $report->last()->distancem;

            $dataReport = [
                'vehicle' => $dispatchRegister->vehicle,
                'plate' => $dispatchRegister->plate,
                'vehicleSpeed' => round($report->last()->location->speed,2),
                'route' => $dispatchRegister->route->name,
                'routeDistance' => $routeDistance,
                'routePercent' => round(($totalDistance/$routeDistance)*100,1),
                'dates' => $report->pluck('date'),
                'times' => $report->pluck('timed'),
                'distances' => $report->pluck('distancem'),
                'values' => $report->pluck('status_in_minutes'),
                'latitudes' => $report->pluck('location.latitude'),
                'longitudes' => $report->pluck('location.longitude')
            ];
        }

        return response()->json($dataReport);
    }

    public function ajax(Request $request)
    {
        switch ($request->get('option')){
            case 'loadRoutes':
                $company = $request->get('company');
                $routes = $company!='null'?Route::where('id_empresa', '=', $company)->orderBy('nombre','asc')->get():[];
                return view('reports.routeSelect', compact('routes'));
            break;
            default:
                return "Nothing to do";
            break;
        }
    }
}
