<?php

namespace App\Http\Controllers;

use App\Company;
use App\ControlPoints;
use App\DispatchRegister;
use App\Route;
use App\RouteGoogle;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $companies = Company::where('estado', '=', true)->orderBy('des_corta','asc')->get();
        return view('reports.route', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $roundTripDispatchRegisters = DispatchRegister::where('fecha', '=', $request->get('date-report'))
            ->where('id_ruta', '=', $request->get('route-report'))
            ->get()->groupBy('n_vuelta');

        return view('reports.routeReport', compact('roundTripDispatchRegisters'));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @return \Illuminate\Http\JsonResponse
     */
    public function chart(DispatchRegister $dispatchRegister)
    {
        $dataReport = ['empty'=>true];
        $report = $dispatchRegister->reports->sortBy('date');

        if( $report->isNotEmpty() ){
            $routeDistance = $dispatchRegister->route->distance*1000;
            $totalDistance = $report->last()->distancem;
            $controlPoints = ControlPoints::where('id_ruta','=',$dispatchRegister->route->id)->get();
            $urlLayerMap = RouteGoogle::find($dispatchRegister->route->id);
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
                'longitudes' => $report->pluck('location.longitude'),
                'controlPoints' => $controlPoints,
                'urlLayerMap' => $urlLayerMap?$urlLayerMap->url:''
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
