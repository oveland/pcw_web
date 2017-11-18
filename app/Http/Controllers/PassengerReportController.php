<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Models\Passengers\PassengerCounterPerDay;
use App\Models\Passengers\PassengerCounterPerDaySixMonth;
use App\Models\Passengers\RecorderCounterPerDay;
use App\Route;
use App\Services\PCWExporter;
use App\Vehicle;
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
        return view('reports.passengers.consolidated.days.index', compact('companies'));
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

        return view('reports.passengers.consolidated.days.passengersReport', compact('passengerReport'));
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
        $ageReport = Carbon::parse($dateReport)->diffInDays(Carbon::now());
        $model = $ageReport <= 5 ? PassengerCounterPerDay::class : PassengerCounterPerDaySixMonth::class;
        $passengersCounterPerDay = $model::where('date', $dateReport)
            ->where('company_id', $company->id)
            ->get();

        /*
        // Query passenger by recorder counter
        $recorderCounterPerDays = RecorderCounterPerDay::where('date', $dateReport)
            ->where('company_id', $company->id)
            ->get();
        */

        $recorderCounterPerDays = $this->buildPassengersByRecorder($company, $dateReport);

        // Build report data
        $reports = array();
        foreach ($recorderCounterPerDays->report as $recorderCounterPerDay) {
            $sensor = $passengersCounterPerDay->where('vehicle_id', $recorderCounterPerDay->vehicle->id)->first();

            $reports[] = (object)[
                'plate' => $recorderCounterPerDay->vehicle->plate,
                'number' => $recorderCounterPerDay->vehicle->number,
                'date' => $dateReport,
                'passengers' => (object)[
                    'sensor' => $sensor ? $sensor->total : 0,
                    'recorder' => $recorderCounterPerDay->passengers ?? 0,
                    'start_recorder' => $recorderCounterPerDay->start_recorder
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

        $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id');

        $report = array();
        $issues = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicle_id => $dispatchRegistersByVehicle) {
            $firstDispatchRegisterByVehicle = $dispatchRegistersByVehicle->first();
            $start_recorder = $firstDispatchRegisterByVehicle->start_recorder;
            $first_start_recorder = $start_recorder;

            $totalPassengersByVehicle = 0;

            $lastDispatchRegister = null;
            $last_route_id = null;
            foreach ($dispatchRegistersByVehicle as $dispatchRegister) {
                $start_recorder = $dispatchRegister->start_recorder > 0 ? $dispatchRegister->start_recorder : $start_recorder;

                /* For change route on prev dispatch register */
                if( $last_route_id && $last_route_id != $dispatchRegister->route->id ){
                    $endRecorderByOtherRoutes = $dispatchRegisters
                            ->where('vehicle_id', $vehicle_id)
                            ->where('id', '<', $dispatchRegister->id)
                            ->where('id', '>=', $lastDispatchRegister->id ?? 0)
                            ->last()->end_recorder ?? null;

                    $start_recorder = $endRecorderByOtherRoutes > $start_recorder ? $endRecorderByOtherRoutes : $start_recorder;
                }

                $end_recorder = $dispatchRegister->end_recorder;
                $passengersByRoundTrip = $end_recorder - $start_recorder;

                $totalPassengersByVehicle += $passengersByRoundTrip;

                $issue = $start_recorder <= 0 ? __('Start Recorder') : ($end_recorder <= 0 ? __('End Recorder') : ($passengersByRoundTrip > 1000 ? __('High count') : null));
                if ($issue) {
                    $issues->push((object)[
                        'field' => $issue,
                        'route_id' => $dispatchRegister->route_id,
                        'vehicle_id' => $vehicle_id,
                        'start_recorder' => $start_recorder,
                        'end_recorder' => $end_recorder,
                        'passengers' => $passengersByRoundTrip,
                        'dispatchRegister' => $dispatchRegister
                    ]);
                }
                $start_recorder = $end_recorder > 0 ? $end_recorder : $start_recorder;

                $lastDispatchRegister = $dispatchRegister;
                $last_route_id = $dispatchRegister->route->id;
            }
            $vehicle = Vehicle::find($vehicle_id);
            $report[] = (object)[
                'vehicle' => $vehicle,
                'start_recorder' => $first_start_recorder,
                'passengers' => $totalPassengersByVehicle,
                'issue' => $issues->where('vehicle_id', $vehicle_id)->first()->field ?? null
            ];
        }

        return (object)[
            'report' => collect($report)->sortBy(function ($report) {
                return $report->vehicle->number;
            }),
            'issues' => $issues
        ];
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
