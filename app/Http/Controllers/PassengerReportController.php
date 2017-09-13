<?php

namespace App\Http\Controllers;

use App\Company;
use App\Models\Passengers\PassengerCounterPerDay;
use App\Models\Passengers\PassengerCounterPerDaySixMonth;
use App\Models\Passengers\RecorderCounterPerDays;
use App\Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class PassengerReportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('passengers.general.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $dateReport = $request->get('date-report');
        $ageReport = Carbon::parse($dateReport)->diffInDays(Carbon::now());

        $model = $ageReport <= 5 ? PassengerCounterPerDay::class : PassengerCounterPerDaySixMonth::class;

        $passengersCounterPerDay = $model::where('date', $dateReport)
            ->where('company_id', $company->id)
            ->get();

        $recorderCounterPerDays = RecorderCounterPerDays::where('date', $dateReport)
            ->where('company_id', $company->id)
            ->get();

        $reports = array();
        foreach ($recorderCounterPerDays as $recorderCounterPerDay) {
            $sensor = $passengersCounterPerDay->where('vehicle_id', $recorderCounterPerDay->vehicle_id)->first();

            $reports[] = (object)[
                'plate' => $recorderCounterPerDay->vehicle->plate,
                'number' => $recorderCounterPerDay->vehicle->number,
                'passengers' => (object)[
                    'sensor' => $sensor ? $sensor->total : 0,
                    'recorder' => $recorderCounterPerDay->passengers ?? 0
                ]
            ];
        }

        return view('passengers.general.passengersReport', compact('reports'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function ajax($action, Request $request)
    {
        switch ($action) {
            case 'loadRoutes':
                if (Auth::user()->isAdmin()) {
                    $company = $request->get('company');
                } else {
                    $company = Auth::user()->company->id;
                }
                $routes = $company != 'null' ? Route::whereCompanyId($company)->orderBy('name')->get() : [];
                return view('passengers.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
