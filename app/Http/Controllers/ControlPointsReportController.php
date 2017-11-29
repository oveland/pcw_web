<?php

namespace App\Http\Controllers;

use App\Company;
use App\ControlPointTimeReport;
use App\DispatchRegister;
use App\Http\Controllers\Utils\StrTime;
use App\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlPointsReportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.route.control-points.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchReport(Request $request)
    {
        $dateReport = $request->get('date-report');
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $route = Route::find($request->get('route-report'));

        if (!$route->belongsToCompany($company)) abort(404);

        $dispatchRegisters = DispatchRegister::where('date', '=', $dateReport)
            ->where('route_id', '=', $route->id)
            ->where(function ($query) {
                $query->where('status', '=', 'En camino')->orWhere('status', '=', 'Terminó');
            })
            ->orderBy('departure_time')
            ->get();

        $controlPointTimeReports = ControlPointTimeReport::whereIn('dispatch_register_id', $dispatchRegisters->pluck('id'))
            ->get();

        $controlPointTimeReportsByRoundTrip = $controlPointTimeReports->groupBy(function ($controlPointTimeReport) {
            return $controlPointTimeReport->dispatchRegister->round_trip;
        });

        return view('reports.route.control-points.ControlPointTimesByRoundTrip', compact('controlPointTimeReportsByRoundTrip'));
    }

    function export()
    {

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('partials.selects.routes', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
