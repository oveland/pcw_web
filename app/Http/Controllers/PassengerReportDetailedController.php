<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use Excel;
use App\Route;
use App\Services\PCWExporter;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassengerReportDetailedController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.passengers.detailed.days.index', compact('companies'));
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

        return view('reports.passengers.detailed.days.passengersReport', compact('passengerReport'));
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

        Excel::create(__('Passengers by Route') . " $dateReport", function ($excel) use ($passengerReports, $dateReport, $company) {
            foreach ($passengerReports->reports as $routeId => $passengerReportByRoute) {
                $route = Route::find($routeId);

                $dataExcel = array();

                foreach ($passengerReportByRoute as $vehicleId => $passengerReport) {
                    $vehicle = Vehicle::find($vehicleId);
                    $dataExcel[] = [
                        __('N°') => count($dataExcel) + 1,                                                                                  # A CELL
                        __('Vehicle') => intval($vehicle->number),                                                                          # C CELL
                        __('Plate') => $vehicle->plate,                                                                                     # D CELL
                        __('Passengers') => $passengerReport->passengers,                                                                   # D CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => __('Passengers by Route') . " $dateReport",
                    'title' => __('Passengers by Route') . " $dateReport",
                    'subTitle' => "$route->name",
                    'sheetTitle' => "$route->name",
                    'data' => $dataExcel,
                    'type' => 'passengersReportByRoute'
                ];
                //foreach ()
                /* SHEETS */
                $excel = PCWExporter::createHeaders($excel, $dataExport);
                $excel = PCWExporter::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
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
            ->sortBy('departure_time');

        $dispatchRegistersByRoutes = $dispatchRegisters
            ->sortBy(function($dispatchRegister,$key){
                return $dispatchRegister->route->name;
            })
            ->groupBy('route_id');

        $reports = array();
        $issues = collect([]);
        foreach ($dispatchRegistersByRoutes as $route_id => $dispatchRegistersByRoute) {
            $dispatchRegistersByVehicles = $dispatchRegistersByRoute->sortBy('departure_time')->groupBy('vehicle_id');

            $report = array();
            foreach ($dispatchRegistersByVehicles as $vehicle_id => $dispatchRegistersByVehicle) {
                $firstDispatchRegisterByVehicle = $dispatchRegistersByVehicle->first();
                $start_recorder = $firstDispatchRegisterByVehicle->start_recorder;
                $first_start_recorder = $start_recorder;

                $totalPassengersByVehicle = 0;
                $lastDispatchRegister = null;
                foreach ($dispatchRegistersByVehicle as $dispatchRegister) {
                    /* For change route on prev dispatch register */
                    $endRecorderByOtherRoutes = $dispatchRegisters
                            ->where('vehicle_id', $vehicle_id)
                            ->where('id', '<', $dispatchRegister->id)
                            ->where('id', '>', $lastDispatchRegister->id ?? 0)
                            ->last()->end_recorder ?? null;

                    $start_recorder = $dispatchRegister->start_recorder > 0 ? $dispatchRegister->start_recorder : $start_recorder;
                    $start_recorder = $endRecorderByOtherRoutes > $start_recorder ? $endRecorderByOtherRoutes : $start_recorder;

                    $end_recorder = $dispatchRegister->end_recorder;
                    $passengersByRoundTrip = $end_recorder - $start_recorder;

                    $totalPassengersByVehicle += $passengersByRoundTrip;

                    $issue = $start_recorder <= 0 ? __('Start Recorder') : ($end_recorder <= 0 ? __('End Recorder') : ($passengersByRoundTrip > 1000 ? __('High count') : null));
                    if ($issue) {
                        $issues->push((object)[
                            'field' => $issue,
                            'route_id' => $route_id,
                            'vehicle_id' => $vehicle_id,
                            'start_recorder' => $start_recorder,
                            'end_recorder' => $end_recorder,
                            'passengers' => $passengersByRoundTrip,
                            'dispatchRegister' => $dispatchRegister
                        ]);
                    }
                    $start_recorder = $end_recorder > 0 ? $end_recorder : $start_recorder;
                    $lastDispatchRegister = $dispatchRegister;
                }

                $report[$vehicle_id] = (object)[
                    'start_recorder' => $first_start_recorder,
                    'passengers' => $totalPassengersByVehicle,
                    'issue' => $issues->where('vehicle_id', $vehicle_id)->first()->field ?? null
                ];
            }

            $reports[$route_id] = $report;
        }

        $passengerReport = (object)[
            'date' => $dateReport,
            'companyId' => $company->id,
            'reports' => $reports,
            'issues' => collect($issues)->groupBy('route_id'),
        ];

        return $passengerReport;
    }
}
