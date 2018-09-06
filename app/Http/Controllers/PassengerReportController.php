<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Models\Passengers\PassengerCounterPerDaySixMonth;
use App\Route;
use App\Services\PCWExporter;
use App\Traits\CounterByRecorder;
use App\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class PassengerReportController extends Controller
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

        $passengerReport = $this->buildPassengerReport($company, $dateReport);

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

        $passengerReports = $this->buildPassengerReport($company, $dateReport);

        $dataExcel = array();
        foreach ($passengerReports->reports as $report) {
            $vehicle = Vehicle::find($report->vehicle_id);
            $sensor = $report->passengers->sensor;
            $recorder = $report->passengers->recorder;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Recorder') => intval($recorder),                                    # D CELL
                __('Sensor') => intval($sensor),                                        # E CELL
                __('Difference') => abs($sensor - $recorder),                   # F CELL
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
        $passengersCounterPerDay = PassengerCounterPerDaySixMonth::where('date', $dateReport)
            ->where('company_id', $company->id)
            ->get();

        $recorderCounterPerDays = $this->buildPassengersByRecorder($company, $dateReport);

        // Build report data
        $reports = array();
        foreach ($recorderCounterPerDays->report as $recorderCounterPerDay) {
            $vehicle = $recorderCounterPerDay->vehicle;
            $sensor = $passengersCounterPerDay->where('vehicle_id', $vehicle->id)->first();

            $reports[] = (object)[
                'vehicle_id' => $vehicle->id,
                'date' => $dateReport,
                'passengers' => (object)[
                    'sensor' => $sensor ? $sensor->total : 0,
                    'recorder' => $recorderCounterPerDay->passengers ?? 0,
                    'start_recorder' => $recorderCounterPerDay->start_recorder,
                    'issue' => $recorderCounterPerDay->issue
                ]
            ];
        }

        $passengerReport = (object)[
            'date' => $dateReport,
            'companyId' => $company->id,
            'reports' => $reports,
            'issues' => $recorderCounterPerDays->issues,
        ];

        return $passengerReport;
    }

    /*
     * Build Passengers by Recorder and check for issues
     */
    public function buildPassengersByRecorder($company, $dateReport)
    {
        $routes = Route::where('company_id', $company->id)->get();
        $dispatchRegisters = DispatchRegister::whereIn('route_id', $routes->pluck('id'))->where('date', $dateReport)->active()->get()
            ->sortBy('id');

        return self::report($dispatchRegisters);
    }

    static function report($dispatchRegisters){
        return CounterByRecorder::report($dispatchRegisters);
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
