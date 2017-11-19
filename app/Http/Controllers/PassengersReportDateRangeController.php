<?php

namespace App\Http\Controllers;

use App\DispatchRegister;
use App\Models\Passengers\RecorderCounterPerDay;
use App\Route;
use App\Services\PCWExporter;
use App\Traits\CounterByRecorder;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\Company;

class PassengersReportDateRangeController extends Controller
{
    use CounterByRecorder;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.passengers.consolidated.dates.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');

        if ($finalDate < $initialDate) return view('partials.dates.invalidRange');

        $passengerReport = $this->buildPassengerReport($company, $initialDate, $finalDate);

        return view('reports.passengers.consolidated.dates.passengersReport', compact('passengerReport'));
    }

    /**
     * Build passenger report from company and date
     *
     * @param $company
     * @param $initialDate
     * @param $finalDate
     * @return object
     */
    public function buildPassengerReport($company, $initialDate, $finalDate)
    {
        $routes = Route::where('company_id', $company->id)->get();
        $dispatchRegisters = DispatchRegister::whereIn('route_id', $routes->pluck('id'))->whereBetween('date', [$initialDate, $finalDate])->active()->get()
            ->sortBy('departure_time');

        $reports = self::report($dispatchRegisters);

        $passengerReport = (object)[
            'initialDate' => $initialDate,
            'finalDate' => $finalDate,
            'companyId' => $company->id,
            'reports' => $reports,
        ];

        return $passengerReport;
    }

    /**
     * Export report to excel format
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');

        $passengerReports = $this->buildPassengerReport($company, $initialDate, $finalDate);

        $dataExcel = array();
        foreach ($passengerReports->reports as $date => $report) {
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Date') => $date,                               # B CELL
                __('Recorder') => $report->total,                                    # F CELL
            ];
        }

        PCWExporter::excel([
            'fileName' => __('Consolidated per date range'),
            'title' => __('Passengers report' . "\n $initialDate - $finalDate"),
            'subTitle' => __('Consolidated per date range'),
            'data' => $dataExcel,
            'type' => 'passengerReportByRangeTotalFooter'
        ]);
    }

    static function report($dispatchRegisters)
    {
        $dispatchRegistersByDates = $dispatchRegisters->groupBy('date');

        $reports = array();
        foreach ($dispatchRegistersByDates as $date => $dispatchRegistersByDate) {
            $reportByDate = PassengerReportController::report($dispatchRegistersByDate);

            $reports[$date] = (object)[
                'date' => $date,
                'total' => $reportByDate->report->sum('passengers'),
                'issues' => $reportByDate->issues,
            ];
        }

        return collect($reports)->sortBy('date');
    }
}
