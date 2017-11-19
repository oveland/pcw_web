<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Traits\CounterByRecorder;
use Excel;
use App\Route;
use App\Services\PCWExporter;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassengerReportDetailedController extends Controller
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
                $report = $passengerReportByRoute->report;

                $dataExcel = array();

                foreach ($report as $vehicleId => $passengerReport) {
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

        $reports = self::report($dispatchRegisters);

        $passengerReport = (object)[
            'date' => $dateReport,
            'companyId' => $company->id,
            'reports' => $reports
        ];

        return $passengerReport;
    }

    static function report($dispatchRegisters)
    {
        $dispatchRegistersByRoutes = $dispatchRegisters
            ->sortBy(function ($dispatchRegister, $key) {
                return $dispatchRegister->route->name;
            })
            ->groupBy('route_id');

        $reports = array();
        foreach ($dispatchRegistersByRoutes as $route_id => $dispatchRegistersByRoute) {
            $dispatchRegistersByVehicles = $dispatchRegistersByRoute->sortBy('departure_time')->groupBy('vehicle_id');

            $report = array();
            $issues = array();
            foreach ($dispatchRegistersByVehicles as $vehicle_id => $dispatchRegistersByVehicle) {
                $totalByVehicle = self::totalByVehicle($vehicle_id, $dispatchRegisters, $dispatchRegistersByVehicle);
                $report[$vehicle_id] = $totalByVehicle->report;
                $totalByVehicle->issues->isNotEmpty() ? $issues[$vehicle_id] = $totalByVehicle->issues : null;
            }

            $reports[$route_id] = (object)[
                'report' => $report,
                'issues' => $issues,
            ];
        }

        return $reports;
    }
}
