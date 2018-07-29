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

class GeolocationPassengersReportController extends Controller
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
        $dispatchRegister = DispatchRegister::find($request->get('dispatch-register-id'));
        $vehicle = $dispatchRegister->vehicle;
        $route = $dispatchRegister->route;
        $date = $dispatchRegister->date;

        $dispatchRegistersByVehicle = DispatchRegister::findAllByDateAndVehicleAndRoute($date, $vehicle->id, $route->id);
        $counterByRecorder = CounterByRecorder::totalByVehicle($vehicle->id, $dispatchRegistersByVehicle, $dispatchRegistersByVehicle);

        $passengers = Passenger::where('dispatch_register_id', $dispatchRegister->id)->orderBy('date')->get();

        $data = collect([]);
        foreach ($passengers as $passenger) {
            $data->push((object)[
                'time' => $passenger->date->toTimeString(),
                'totalSensorRecorder' => $passenger->total_sensor_recorder,
                'total' => $passenger->total,
                'totalFrontSensor' => $passenger->total_front_sensor,
                'totalBackSensor' => $passenger->total_back_sensor,
                'totalCount' => $passenger->totalCount(),
                'latitude' => $passenger->latitude,
                'longitude' => $passenger->longitude,
                'frame' => $passenger->frame,
                'vehicleStatus' => $passenger->vehicleStatus
            ]);
        }

        $report = collect([
            'route' => $dispatchRegister->route,
            'totalBySensorRecorder' => $data->isNotEmpty() ? ($data->last()->totalSensorRecorder - $data->first()->totalSensorRecorder) : 0,
            'counterByRecorder' => $counterByRecorder->report->history[$dispatchRegister->id],
            'data' => $data
        ]);

        return response()->json($report);
    }
}
