<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Services\PCWExporterService;
use App\Services\Reports\Passengers\PassengersService as PassengersReporter;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class ReportPassengerRecorderConsolidatedDailyController extends Controller
{
    /**
     * @var PassengersReporter
     */
    private $passengersReporter;

    /**
     * ReportPassengerRecorderConsolidatedDailyController constructor.
     * @param PassengersReporter $passengersReporter
     */
    public function __construct(PassengersReporter $passengersReporter)
    {
        $this->passengersReporter = $passengersReporter;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }

        return view('reports.passengers.recorders.consolidated.days.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $dateReport = $request->get('date-report');

        $passengerReport = $this->passengersReporter->consolidated->buildDailyReport($company, $dateReport);

        return view('reports.passengers.recorders.consolidated.days.passengersReport', compact('passengerReport'));
    }

    /**
     * Export report to excel format
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $dateReport = $request->get('date-report');

        $passengerReports = $this->passengersReporter->consolidated->buildDailyReport($company, $dateReport);

        $this->passengersReporter->consolidated->exportDailyReportFiles($passengerReports);
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
                $routes = $company != 'null' ? Route::active()->whereCompanyId($company)->orderBy('name')->get() : [];
                return view('partials.selects.routes', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
