<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Services\Reports\Passengers\PassengersService as PassengersReporter;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;


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
     * @return Factory|View
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
     * @return Factory|View
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
     * @return string
     */
    public function export(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $dateReport = $request->get('date-report');

        $passengerReports = $this->passengersReporter->consolidated->buildDailyReport($company, $dateReport);

        return $this->passengersReporter->consolidated->exportDailyReportFiles($passengerReports);
    }
}
