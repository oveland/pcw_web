<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatcherVehicle;
use App\Http\Requests\StoreDispatcherVehicle;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoDispatcherController extends Controller
{
    /**
     * @var GeneralController
     */
    private $generalController;

    public function __construct(GeneralController $generalController)
    {
        $this->generalController = $generalController;
    }

    public function index(Request $request)
    {
        $companies = Auth::user()->isAdmin() ? Company::findAllActive() : null;
        return view('operation.dispatch.auto-dispatcher.index', compact('companies'));
    }

    public function show(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $routes = $company->activeRoutes;
        $dispatches = $company->dispatches;

        $unassignedVehicles = $company->vehicles()
            ->active()
            ->whereNotIn('id', DispatcherVehicle::active()->pluck('vehicle_id'))
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

        if ($request->get('delete') === 'true') {
            $response->message = __('The Route has ben unassigned successfully');

            if ($dispatcherVehicle) {
                DB::delete("DELETE FROM auto_dispatch_registers WHERE dispatcher_vehicle_id = $dispatcherVehicle->id");
                if (!$dispatcherVehicle->delete()) {
                    $response->success = false;
                    $response->message = __('An error occurred in the process. Contact your administrator');
                }
            }
        } else {
            if (!$dispatcherVehicle) {
                $dispatcherVehicle = new DispatcherVehicle();
                $dispatcherVehicle->vehicle_id = $vehicle->id;
                $dispatcherVehicle->day_type_id = 1;
            }
            $dispatcherVehicle->default = true;

            $dispatcherVehicle->date = Carbon::now();
            $dispatcherVehicle->route_id = $route->id;
            $dispatcherVehicle->dispatch_id = $dispatch->id;

            if (!$dispatcherVehicle->save()) {
                $response->success = false;
                $response->message = __('An error occurred in the process. Contact your administrator');
            }
        }

        return response()->json($response);
    }
}
