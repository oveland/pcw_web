<?php

namespace App\Http\Controllers;

use App\Company;
use App\CounterIssue;
use App\Passenger;
use Auth;
use DB;
use Illuminate\Http\Request;

class StatusCounterController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('admin.counter.status.index', compact('companies'));
    }

    public function list(Request $request)
    {
        $companyReport = $request->get('company-report');
        $typeReport = $request->get('type-report');
        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;

        switch ($typeReport) {
            case 'issues':
                $initialDate = $request->get('initial-date');
                $finalDate = $request->get('final-date');
                if ($initialDate > $finalDate) return view('partials.dates.invalidRange');

                $vehicles = $company->vehicles;

                $counterIssues = CounterIssue::whereIn('vehicle_id', $vehicles->pluck('id'))
                    ->whereBetween('date', [$initialDate, $finalDate])
                    ->orderBy('id')
                    ->limit(1000)
                    ->get();

                $counterIssuesByVehicles = $counterIssues->groupBy('vehicle_id');

                return view('admin.counter.status.listIssues', compact('counterIssuesByVehicles'));
                break;
            case 'history':
                $initialDate = $request->get('initial-date');
                $finalDate = $request->get('final-date');
                if ($initialDate > $finalDate) return view('partials.dates.invalidRange');

                $vehicles = $company->vehicles;

                $passengers = Passenger::whereIn('vehicle_id', $vehicles->pluck('id'))
                    ->whereBetween('date', [$initialDate, $finalDate])
                    ->orderBy('id')
                    ->limit(1000)
                    ->get();

                $passengersByVehicles = $passengers->groupBy('vehicle_id');

                return view('admin.counter.status.listHistory', compact('passengersByVehicles'));
                break;
            case 'route':
                $routeReportDate = $request->get('route-report-date');
                $routeReport = $request->get('route-report');

                $passengers = Passenger::whereBetween('date', ["$routeReportDate 00:00:00", "$routeReportDate 23:59:59"])
                    ->where('dispatch_register_id', '<>', null)
                    ->orderBy('id')
                    ->limit(1000)
                    ->get();

                $passengersByRoute = $passengers->filter(function ($passenger, $key) use ($routeReport) {
                    return $passenger->dispatchRegister->route->id == $routeReport;
                });

                $passengersByRoundTrip = $passengersByRoute->groupBy(function ($passenger, $key) {
                    return $passenger->dispatchRegister->round_trip;
                });

                return view('admin.counter.status.listByRoute', compact('passengersByRoundTrip'));
                break;
        }
    }

    public function showCounterIssue(CounterIssue $counterIssue)
    {
        return view('admin.counter.status._issue', compact('counterIssue'));
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
