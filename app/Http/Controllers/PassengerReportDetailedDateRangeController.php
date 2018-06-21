<?php

namespace App\Http\Controllers;

use Excel;
use Illuminate\Http\Request;
use App\PassengersDispatchRegister;
use App\Route;
use App\Services\PCWExporter;
use App\Traits\CounterByRecorder;
use Carbon\Carbon;
use Auth;
use App\Company;

class PassengerReportDetailedDateRangeController extends Controller
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
        return view('reports.passengers.detailed.dates.index', compact('companies'));
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

        return view('reports.passengers.detailed.dates.passengersReport', compact('passengerReport'));
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
            ->sortBy('id');

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

        Excel::create(__('Detailed') . " $initialDate - $finalDate", function ($excel) use ($passengerReports, $initialDate, $finalDate, $company) {
            foreach ($passengerReports->reports as $routeId => $passengerReportByRoute) {
                $route = Route::find($routeId);
                $report = $passengerReportByRoute->report;

                $dataExcel = array();
                foreach ($report as $date => $passengerReport) {
                    $dataExcel[] = [
                        __('N°') => count($dataExcel) + 1,                                                                                  # A CELL
                        __('Date') => $date,                                                                                                # B CELL
                        __('Passengers') => $passengerReport->total,                                                                        # C CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => __('Detailed') . " $initialDate - $finalDate",
                    'title' => __('Detailed per date range'),
                    'subTitle' => "$initialDate - $finalDate | $route->name",
                    'sheetTitle' => "$route->name",
                    'data' => $dataExcel,
                    'type' => 'passengerReportByRangeTotalFooter'
                ];
                $excel = PCWExporter::createHeaders($excel, $dataExport);
                $excel = PCWExporter::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
    }

    static function report($dispatchRegisters)
    {
        $dispatchRegistersByRoutes = $dispatchRegisters->sortBy('id')->groupBy('route_id');

        $reports = array();
        foreach ($dispatchRegistersByRoutes as $route_id => $dispatchRegistersByRoute) {
            $dispatchRegistersByDates = $dispatchRegistersByRoute->sortBy('id')->groupBy('date');

            $reportsByDate = array();
            foreach ($dispatchRegistersByDates as $date => $dispatchRegistersByDate) {
                $date = Carbon::createFromFormat(config('app.date_format'), $date)->format('Y-m-d');
                $report = CounterByRecorder::report($dispatchRegistersByDate);

                $reportsByDate[$date] = (object)[
                    'date' => Route::find($route_id),
                    'total' => $report->report->sum('passengers'),
                    'issues' => $report->issues,
                ];
            }

            $reportsByDate = collect($reportsByDate);

            $reports[$route_id] = (object)[
                'route' => Route::find($route_id),
                'total' => $reportsByDate->sum('total'),
                'report' => $reportsByDate
            ];
        }

        return collect($reports)->sortBy(function ($report, $key) {
            return $report->route->name;
        });
    }
}
