<?php

namespace App\Http\Controllers;

use App\Company;
use App\Models\Passengers\PassengerCounterPerDay;
use App\Models\Passengers\PassengerCounterPerDaySixMonth;
use App\Models\Passengers\RecorderCounterPerDay;
use App\Route;
use App\Services\PCWExporter;
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
        return view('reports.passengers.consolidated.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $dateReport = $request->get('date-report');

        $passengerReport = $this->buildPassengerReport($company, $dateReport);

        return view('reports.passengers.consolidated.passengersReport', compact('passengerReport'));
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

        $passengerReports = $this->buildPassengerReport($company, $dateReport);

        $dataExcel = array();
        foreach ($passengerReports->reports as $report) {
            $sensor = $report->passengers->sensor;
            $recorder = $report->passengers->recorder;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($report->number),                               # B CELL
                __('Plate') => $report->plate,                                          # C CELL
                __('Start Recorder') => intval($report->passengers->start_recorder),    # D CELL
                __('End Recorder') => intval($report->passengers->end_recorder),        # E CELL
                __('Recorder') => intval($recorder),                                    # F CELL
                __('Sensor') => intval($sensor),                                        # G CELL
                __('Difference') => abs($sensor - $recorder),                   # H CELL
            ];
        }

        PCWExporter::excel([
            'fileName' => __('Passengers report') . " $dateReport",
            'title' => __('Passengers report') . " $dateReport",
            'subTitle' => __('Consolidated per day'),
            'data' => $dataExcel,
            'type' => 'passengerReportTotalFooter'
        ]);
    }

    /**
     * Build passenger report from company and date
     *
     * @param $company
     * @param $dateReport
     * @return object
     */
    public function buildPassengerReport($company, $dateReport)
    {
        // Query passenger by sensor counter
        $ageReport = Carbon::parse($dateReport)->diffInDays(Carbon::now());
        $model = $ageReport <= 5 ? PassengerCounterPerDay::class : PassengerCounterPerDaySixMonth::class;
        $passengersCounterPerDay = $model::where('date', $dateReport)
            ->where('company_id', $company->id)
            ->get();

        // Query passenger by recorder counter
        $recorderCounterPerDays = RecorderCounterPerDay::where('date', $dateReport)
            ->where('company_id', $company->id)
            ->get();

        // Build report data
        $reports = array();
        foreach ($recorderCounterPerDays as $recorderCounterPerDay) {
            $sensor = $passengersCounterPerDay->where('vehicle_id', $recorderCounterPerDay->vehicle_id)->first();

            $reports[] = (object)[
                'plate' => $recorderCounterPerDay->vehicle->plate,
                'number' => $recorderCounterPerDay->vehicle->number,
                'date' => $dateReport,
                'passengers' => (object)[
                    'sensor' => $sensor ? $sensor->total : 0,
                    'recorder' => $recorderCounterPerDay->passengers ?? 0,
                    'start_recorder' => ($recorderCounterPerDay->start_recorder == 0 ? $recorderCounterPerDay->start_recorder_prev : $recorderCounterPerDay->start_recorder) ?? 0,
                    'date_start_recorder' => $recorderCounterPerDay->date_start_recorder_prev ?? 0,
                    'end_recorder' => $recorderCounterPerDay->end_recorder ?? 0
                ]
            ];
        }

        $passengerReport = (object)[
            'date' => $dateReport,
            'companyId' => $company->id,
            'reports' => $reports,
        ];

        return $passengerReport;
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
                return view('reports.passengers.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
