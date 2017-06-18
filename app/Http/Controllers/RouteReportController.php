<?php

namespace App\Http\Controllers;

use App\Company;
use App\ControlPoint;
use App\DispatchRegister;
use App\Route;
use App\RouteGoogle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteReportController extends Controller
{
    const DISPATCH_COMPLETE = 'Terminó';
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if( Auth::user()->isAdmin() ){
            $companies = Company::where('active', '=', true)->orderBy('short_name','asc')->get();
        }
        return view('reports.route', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $route_id = $request->get('route-report');
        $roundTripDispatchRegisters = DispatchRegister::where('date', '=', $request->get('date-report'))
            ->where('route_id', '=', $route_id)->where('status','=',self::DISPATCH_COMPLETE)
            ->orderBy('round_trip','asc')->get()->groupBy('round_trip');

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
            $controlPoints = ControlPoint::where('route_id','=',$dispatchRegister->route->id)->get();
            $urlLayerMap = RouteGoogle::find($dispatchRegister->route->id);
            $dataReport = [
                'vehicle' => $dispatchRegister->vehicle->number,
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function ajax(Request $request)
    {
        switch ($request->get('option')){
            case 'loadRoutes':
                if(Auth::user()->isAdmin()){
                    $company = $request->get('company');
                }else{
                    $company = Auth::user()->company->id;
                }
                $routes = $company!='null'?Route::where('company_id', '=', $company)->orderBy('name','asc')->get():[];
                return view('reports.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
