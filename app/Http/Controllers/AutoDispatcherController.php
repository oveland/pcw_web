<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatcherVehicle;
use App\Http\Requests\StoreDispatcherVehicle;
use App\Route;
use App\Vehicle;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AutoDispatcherController extends Controller
{
    public function index(Request $request)
    {
        $companies = Auth::user()->isAdmin() ? Company::findAllActive() : null;
        return view('operation.dispatch.auto-dispatcher.index', compact('companies'));
    }

    public function show(Request $request)
    {
        $company = GeneralController::getCompany($request);
        $routes = $company->routes;
        $dispatches = $company->dispatches;

        $unassignedVehicles = $company->vehicles()
            ->active()
            ->whereNotIn('id',DispatcherVehicle::all()->pluck('vehicle_id'))
            ->orderBy('number')
            ->get();

        return view('operation.dispatch.auto-dispatcher.show', compact(['dispatches', 'routes', 'unassignedVehicles']));
    }

    public function reassignRoute(StoreDispatcherVehicle $request)
    {
        $response = (object)['success' => true, 'message' => __('The Route has ben reassigned successfully')];
        $vehicle = Vehicle::find($request->get('vehicle_id'));
        $route = Route::find($request->get('route_id'));
        $dispatch = $route->dispatch;

        $dispatcherVehicle = DispatcherVehicle::find($request->get('dispatcher_vehicle_id'));
        if (!$dispatcherVehicle){
            $dispatcherVehicle = new DispatcherVehicle();
            $dispatcherVehicle->vehicle_id = $vehicle->id;
            $dispatcherVehicle->day_type_id = 1;
        }

        $dispatcherVehicle->date = Carbon::now();
        $dispatcherVehicle->route_id = $route->id;
        $dispatcherVehicle->dispatch_id = $dispatch->id;

        if( !$dispatcherVehicle->save() ){
            $response->success = false;
            $response->message = __('An error occurred in the process. Contact your administrator');
        }

        return response()->json($response);
    }
}
