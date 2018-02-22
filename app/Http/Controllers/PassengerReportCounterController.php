<?php

namespace App\Http\Controllers;

use App\Company;
use App\CounterIssue;
use App\Passenger;
use App\Route;
use App\Vehicle;
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
        return view('admin.counter.report.index', compact('companies'));
    }

    public function list(Request $request)
    {
        $companyReport = $request->get('company-report');
        $vehicleReport = $request->get('vehicle-report');
        $typeReport = $request->get('type-report');
        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;
        $vehicle = Vehicle::find($vehicleReport);
        if(!$vehicle || !$vehicle->belongsToCompany($company))return view('errors._403');

        switch ($typeReport) {
            case 'issues':
                $initialDate = $request->get('initial-date');
                $finalDate = $request->get('final-date');
                if ($initialDate > $finalDate) return view('partials.dates.invalidRange');

                $counterIssues = CounterIssue::where('vehicle_id', $vehicle->id)
                    ->whereBetween('date', [$initialDate, $finalDate])
                    ->orderBy('id')
                    ->paginate(config('database.total_pagination'));

                $counterIssues->appends($request->all());

                return view('admin.counter.report.listIssues', compact('counterIssues'));
                break;
            case 'history':
                $initialDate = $request->get('initial-date');
                $finalDate = $request->get('final-date');
                if ($initialDate > $finalDate) return view('partials.dates.invalidRange');

                $passengers = Passenger::where('vehicle_id', $vehicle->id)
                    ->whereBetween('date', [$initialDate, $finalDate])
                    ->orderBy('id')
                    ->paginate(config('database.total_pagination'));

                $passengers->appends($request->all());

                return view('admin.counter.report.listHistory', compact('passengers'));
                break;
            case 'route':
                $routeReportDate = $request->get('route-report-date');
                $routeReport = $request->get('route-report');
                $route = Route::find($routeReport);
                if(!$route || !$route->belongsToCompany($company))return view('errors._403');

                $passengers = Passenger::where('vehicle_id', $vehicle->id)
                    ->whereBetween('date', ["$routeReportDate 00:00:00", "$routeReportDate 23:59:59"])
                    ->where('dispatch_register_id', '<>', null)
                    ->orderBy('id')
                    ->paginate(config('database.total_pagination'));

                $passengers->appends($request->all());

                $passengersByRoute = $passengers->filter(function ($passenger, $key) use ($route) {
                    return $passenger->dispatchRegister->route->id == $route->id;
                });

                $passengersByRoundTrip = $passengersByRoute->groupBy(function ($passenger, $key) {
                    return $passenger->dispatchRegister->round_trip;
                });

                return view('admin.counter.report.listByRoute', compact('passengersByRoundTrip'));
                break;
        }
    }

    public function showCounterIssue(CounterIssue $counterIssue)
    {
        return view('admin.counter.report._issue', compact('counterIssue'));
    }

    public static function compareChangeFrames($currentFrame, $prevFrame)
    {
        $comparedFrame = array();
        $currentFrameFields = explode(' ', $currentFrame);
        $prevFrameFields = explode(' ', $prevFrame ?? $currentFrame);
        foreach ($currentFrameFields as $index => $field) {
            $comparedFrame[] = (object)[
                'class' => (isset($prevFrameFields[$index]) && $field != $prevFrameFields[$index] && ($index < count($currentFrameFields) - 1)) ? 'btn btn-xs btn-success p-2 tooltips' : '',
                'field' => $field,
                'prevField' => $prevFrameFields[$index] ?? ''
            ];
        }
        return $comparedFrame;
    }
}
