<?php

namespace App\Http\Controllers;

use App\Company;
use App\Route;
use App\Services\Reports\Routes\ControlPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlPointsReportController extends Controller
{
    /**
     * @var ControlPointService
     */
    private $controlPointService;

    /**
     * ControlPointsReportController constructor.
     * @param ControlPointService $controlPointService
     */
    public function __construct(ControlPointService $controlPointService)
    {
        $this->controlPointService = $controlPointService;
    }


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
        $company = GeneralController::getCompany($request);
        $dateReport = $request->get('date-report');
        $route = Route::find($request->get('route-report'));

        if (!$route || !$route->belongsToCompany($company)) abort(404);

        $reportsByControlPoints = $this->controlPointService->buildReportsByControlPoints($route, $dateReport);

        switch ($request->get('type-report')) {
            case 'round-trip':
                $controlPointTimeReportsByRoundTrip = $reportsByControlPoints->groupBy(function ($reportsByControlPoints) {
                    return $reportsByControlPoints->dispatchRegister->round_trip;
                });

                return view('reports.route.control-points.ControlPointTimesByRoundTrip', compact(['controlPointTimeReportsByRoundTrip', 'route']));
                break;
            case 'vehicle':
                $controlPointTimeReportsByVehicles = $reportsByControlPoints->groupBy(function ($reportsByControlPoints) {
                    return $reportsByControlPoints->vehicle->id;
                });

                return view('reports.route.control-points.ControlPointTimesByVehicle', compact(['controlPointTimeReportsByVehicles', 'route']));
                break;
            default:
                return view('reports.route.control-points.ControlPointTimesByAll', compact(['reportsByControlPoints', 'route']));
                break;
        }
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
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('partials.selects.routes', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
