<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use App\Services\PCWExporterService;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class ReportPassengerRecorderConsolidatedDailyController extends Controller
{
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
            $sensorRecorder = $report->passengers->sensorRecorder;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Sensor recorder') => intval($sensorRecorder),                       # D CELL
                __('Recorder') => intval($recorder),                                    # E CELL
                __('Sensor') => intval($sensor),                                        # F CELL
            ];
        }

        PCWExporterService::excel([
            'fileName' => __('Passengers report') . " $dateReport",
            'title' => __('Passengers report') . " $dateReport",
            'subTitle' => __('Consolidated per day'),
            'data' => $dataExcel,
            'type' => 'passengerReportTotalFooter'
        ]);
    }

    /**
     * Export and store report to excel format
     * returns tag
     *
     * @param $passengerReports
     * @return string
     */
    public function storeExcel($passengerReports)
    {
        $dateReport = $passengerReports->date;

        $dataExcel = array();
        foreach ($passengerReports->reports as $report) {
            $vehicle = Vehicle::find($report->vehicle_id);
            $sensor = $report->passengers->sensor;
            $recorder = $report->passengers->recorder;
            $sensorRecorder = $report->passengers->sensorRecorder;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Sensor recorder') => intval($sensorRecorder),                       # D CELL
                __('Recorder') => intval($recorder),                                    # E CELL
                __('Sensor') => intval($sensor),                                        # F CELL
            ];
        }

        return PCWExporterService::store([
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
        $routes = Route::where('company_id', $company->id)->get();
        $dispatchRegisters = DispatchRegister::whereIn('route_id', $routes->pluck('id'))->where('date', $dateReport)->active()->get()
            ->sortBy('id');

        $passengerBySensor = CounterBySensor::report($dispatchRegisters);
        $passengerByRecorder = CounterByRecorder::report($dispatchRegisters);

        // Build report data
        $reports = collect([]);
        foreach ($passengerBySensor->report as $vehicleId => $sensor) {
            $recorder = $passengerByRecorder->report["$vehicleId"];

            $reports->push((object)[
                'vehicle_id' => $vehicleId,
                'date' => $dateReport,
                'passengers' => (object)[
                    'sensor' => $sensor->passengersBySensor,
                    'sensorRecorder' => $sensor->passengersBySensorRecorder,
                    'recorder' => $recorder->passengersByRecorder,
                    'start_recorder' => $recorder->start_recorder,
                    'issue' => $recorder->issue
                ]
            ]);
        }

        $passengerReport = (object)[
            'date' => $dateReport,
            'companyId' => $company->id,
            'reports' => $reports,
            'totalReports' => $reports->count(),
            'issues' => $passengerByRecorder->issues,
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
                $routes = $company != 'null' ? Route::active()->whereCompanyId($company)->orderBy('name')->get() : [];
                return view('partials.selects.routes', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
