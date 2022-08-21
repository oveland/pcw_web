<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Passengers\CounterIssue;
use App\Models\Routes\DispatchRegister;
use App\Models\Passengers\Passenger;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Auth;
use DB;
use Illuminate\Http\Request;

class PassengerReportCounterController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.passengers.sensors.index', compact('companies'));
    }

    public function list(Request $request)
    {
        $companyReport = $request->get('company-report');
        $vehicleReport = $request->get('vehicle-report');
        $typeReport = $request->get('type-report');
        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;
        $vehicle = Vehicle::find($vehicleReport);
        if (!$vehicle || !$vehicle->belongsToCompany($company)) return view('errors._403');

        switch ($typeReport) {
            case 'issues':
                $initialDate = $request->get('initial-date');
                $finalDate = $request->get('final-date');
                $typeIssue = $request->get('type-issue');

                if ($initialDate > $finalDate) return view('partials.dates.invalidRange');

                $whereClause[] = ['vehicle_id','=', $vehicle->id];
                switch ($typeIssue){
                    case 'lower-count':
                        $whereClause[] = ['items_issues','like','%lowerCount%'];
                        break;
                    case 'higher-count':
                        $whereClause[] = ['items_issues','like','%higherCount%'];
                        break;
                    case 'alarms':
                        $whereClause[] = ['items_issues','like','%alarms%'];
                        break;
                    case 'cameras':
                        $whereClause[] = ['raspberry_cameras_issues','<>',''];
                        break;
                    case 'signal-check':
                        $whereClause[] = ['raspberry_check_counter_issue','<>',''];
                        break;
                }

                $counterIssues = CounterIssue::where($whereClause)->whereBetween('date', [$initialDate, $finalDate])
                    ->orderBy('id')
                    ->paginate(config('database.total_pagination'));

                $counterIssues->appends($request->all());

                return view('reports.passengers.sensors.listIssues', compact('counterIssues'));
                break;
            case 'history':
                $initialDate = $request->get('initial-date');
                $finalDate = $request->get('final-date');
                if ($initialDate > $finalDate) return view('partials.dates.invalidRange');

                $passengers = Passenger::findAllByDateRange($vehicle->id, $initialDate, $finalDate)->orderBy('id')
                    ->paginate(config('database.total_pagination'));

                $passengers->appends($request->all());

                //$initialPassengerCount = Passenger::findAllByDateRange($vehicle->id, $initialDate, $finalDate)->orderBy('id')->limit(1)->get()->first();
                $initialPassengerCount = Passenger::whereVehicleId($vehicle->id)->where('date', '<', $initialDate)->orderByDesc('date')->limit(1)->get()->first();
                $initialPassengerCount = $initialPassengerCount ?: Passenger::findAllByDateRange($vehicle->id, $initialDate, $finalDate)->orderBy('id')->limit(1)->get()->first();
                $lastPassengerCount = Passenger::findAllByDateRange($vehicle->id, $initialDate, $finalDate)->orderByDesc('id')->limit(1)->get()->first();

                return view('reports.passengers.sensors.listHistory', compact('passengers'))->with([
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

                $initialPassengerCount->total = $initialPassengerCount->dispatchRegister->initial_sensor_counter;

                return view('reports.passengers.sensors.listByRoute', compact('passengers'))->with([
                    'initialPassengerCount' => $initialPassengerCount,
                    'lastPassengerCount' => $lastPassengerCount,
                ]);
                break;
        }
    }

    public function showCounterIssue(CounterIssue $counterIssue)
    {
        return view('reports.passengers.sensors._issue', compact('counterIssue'));
    }

    public static function compareChangeFrames($currentFrame, $prevFrame)
    {
        $comparedFrame = array();
        if( $currentFrame && $prevFrame){
            $currentFrameFields = explode(' ', $currentFrame);
            $prevFrameFields = explode(' ', $prevFrame ?? $currentFrame);
            foreach ($currentFrameFields as $index => $field) {
                $comparedFrame[] = (object)[
                    'class' => (isset($prevFrameFields[$index]) && $field != $prevFrameFields[$index]) ? 'btn btn-xs btn-success p-2 tooltips' : '',
                    'field' => $field,
                    'prevField' => $prevFrameFields[$index] ?? ''
                ];
            }
        }
        return $comparedFrame;
    }
}
