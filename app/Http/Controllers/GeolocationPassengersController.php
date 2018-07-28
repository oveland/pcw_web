<?php

namespace App\Http\Controllers;

use App\DispatchRegister;
use App\Passenger;
use App\Route;
use App\Traits\CounterByRecorder;
use App\Vehicle;
use Auth;
use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GeolocationPassengersController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::findAllActive();
        }
        return view('reports.passengers.geolocation.index', compact('companies'));
    }

    public function search(Request $request)
    {
        $date = $request->get('date-report');
        $route = Route::find($request->get('route-report'));
        $vehicle = Vehicle::find($request->get('vehicle-report'));
        $dispatchRegister = DispatchRegister::find($request->get('dispatch-register-id'));

        $dispatchRegistersByVehicle = DispatchRegister::findAllByDateAndVehicleAndRoute($date, $vehicle->id, $route->id);
        $counterByRecorder = CounterByRecorder::totalByVehicle($vehicle->id, $dispatchRegistersByVehicle, $dispatchRegistersByVehicle);

        $passengers = Passenger::where('dispatch_register_id', $dispatchRegister->id)->get();

        $data = collect([]);
        foreach ($passengers->take(100) as $passenger) {
            $data->push((object)[
                'time' => $passenger->date->toTimeString(),
                'total' => $passenger->total,
                'totalFrontSensor' => $passenger->total_front_sensor,
                'totalBackSensor' => $passenger->total_back_sensor,
                'totalCount' => $passenger->totalCount(),
                'totalSensorRecorder' => $passenger->total_sensor_recorder,
                'latitude' => $passenger->latitude,
                'longitude' => $passenger->longitude,
                'frame' => $passenger->frame,
                'vehicleStatus' => $passenger->vehicleStatus
            ]);
        }

        $report = collect([
            'route' => $dispatchRegister->route,
            'totalBySensorRecorder' => ($data->last()->totalSensorRecorder - $data->first()->totalSensorRecorder),
            'counterByRecorder' => $counterByRecorder->report->history[$dispatchRegister->id],
            'data' => $data
        ]);

        return response()->json($report);
    }
}
