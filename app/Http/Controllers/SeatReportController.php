<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Passengers\CounterIssue;
use App\Models\Passengers\Passenger;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Auth;
use Illuminate\Http\Request;

class SeatReportController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.passengers.sensors.seats.index', compact('companies'));
    }

    public function play(Request $request)
    {
        $company = GeneralController::getCompany($request);
        $vehicleReport = $request->get('vehicle-report');
        $typeReport = $request->get('type-report');
        $vehicle = Vehicle::find($vehicleReport);
        if (!$vehicle || !$vehicle->belongsToCompany($company)) return view('errors._403');

        switch ($typeReport) {
            case 'history':
                $initialDate = $request->get('initial-date');
                $finalDate = $request->get('final-date');
                if ($initialDate > $finalDate) return view('partials.dates.invalidRange');

                $allPassengers = Passenger::findAllByDateRange($vehicle->id, $initialDate, $finalDate)->orderBy('id');

                $initialPassengerCount = $allPassengers->get()->first();
                $lastPassengerCount = $allPassengers->get()->last();
                $passengers = $allPassengers->paginate(1000)->appends($request->all());


                return view('reports.passengers.sensors.seats.listHistory', compact('passengers'))->with([
                    'initialPassengerCount' => $initialPassengerCount,
                    'lastPassengerCount' => $lastPassengerCount,
                ]);
                break;
            case 'route':
                $routeReportDate = $request->get('route-date-report');
                $routeRoundTrip = $request->get('route-round-trip-report');
                $routeReport = $request->get('route-report');
                $route = Route::find($routeReport);
                if (!$route || !$route->belongsToCompany($company)) return view('errors._403');

                $passengers = Passenger::findAllByRoundTrip($vehicle->id, $route->id, $routeRoundTrip, $routeReportDate)->orderBy('passengers.id')
                    ->paginate(config('database.total_pagination'));

                $passengers->appends($request->all());

                $initialPassengerCount = Passenger::findAllByRoundTrip($vehicle->id, $route->id, $routeRoundTrip, $routeReportDate)->orderBy('passengers.id')->limit(1)->get()->first();
                $lastPassengerCount = Passenger::findAllByRoundTrip($vehicle->id, $route->id, $routeRoundTrip, $routeReportDate)->orderByDesc('passengers.id')->limit(1)->get()->first();

                return view('admin.counter.report.listByRoute', compact('passengers'))->with([
                    'initialPassengerCount' => $initialPassengerCount,
                    'lastPassengerCount' => $lastPassengerCount,
                ]);
                break;
        }
    }

    public static function compareChangeFrames($currentFrame, $prevFrame)
    {
        $comparedFrame = array();
        $currentFrameFields = explode(' ', $currentFrame);
        $prevFrameFields = explode(' ', $prevFrame ?? $currentFrame);
        foreach ($currentFrameFields as $index => $field) {
            $comparedFrame[] = (object)[
                'class' => (isset($prevFrameFields[$index]) && $field != $prevFrameFields[$index]) ? 'btn btn-xs btn-success p-2 tooltips' : '',
                'field' => $field,
                'prevField' => $prevFrameFields[$index] ?? ''
            ];
        }
        return $comparedFrame;
    }
}
