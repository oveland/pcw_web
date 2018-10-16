<?php

namespace App\Http\Controllers;

use App\Company;
use App\ControlPointTimeReport;
use App\DispatchRegister;
use App\Http\Controllers\Utils\StrTime;
use App\Route;
use App\Services\pcwserviciosgps\reports\routes\ControlPointService;
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

        $controlPointTimeReports = $this->controlPointService->allControlPointTimeReport($route, $dateReport);

        switch ($request->get('type-report')){
            case 'round-trip':
                $controlPointTimeReportsByRoundTrip = $controlPointTimeReports->groupBy(function ($controlPointTimeReport) {
                    return $controlPointTimeReport->dispatchRegister->round_trip;
                });

                return view('reports.route.control-points.ControlPointTimesByRoundTrip', compact(['controlPointTimeReportsByRoundTrip','route']));
                break;
            case 'vehicle':
                $controlPointTimeReportsByVehicles = $controlPointTimeReports->groupBy('vehicle_id');
                return view('reports.route.control-points.ControlPointTimesByVehicle', compact(['controlPointTimeReportsByVehicles','route']));
                break;
            default:
                return view('reports.route.control-points.ControlPointTimesByAll', compact(['controlPointTimeReports','route']));
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
