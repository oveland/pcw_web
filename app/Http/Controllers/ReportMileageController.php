<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Http\Controllers\Utils\Database;
use App\LocationReport;
use App\Route;
use App\Services\PCWExporter;
use App\Vehicle;
use Auth;
use Excel;
use Illuminate\Http\Request;

class ReportMileageController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.vehicles.mileage.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = GeneralController::getCompany($request);
        $dateReport = $request->get('date-report');

        $mileageReport = $this->buildMileageReport($company, $dateReport);

        if ($request->get('export')) $this->export($mileageReport);

        return view('reports.vehicles.mileage.show', compact(['mileageReport', 'stringParams']));
    }

    public function buildMileageReport(Company $company = null, $dateReport)
    {
        $vehicles = $company->vehicles;
        $locationReportModel = Database::findLocationReportModelInstanceByDate($dateReport);

        $locationReports = $locationReportModel::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereBetween('date', ["$dateReport 00:00:00", "$dateReport 23:59:59"])
            ->orderBy('location_id')
            ->get();

        $locationReportsByVehicle = $locationReports->groupBy('vehicle_id');
        $reports = collect([]);
        foreach ($locationReportsByVehicle as $vehicleId => $locationReport) {
            $vehicle = Vehicle::find($vehicleId);

            $mileageByRoutes = collect([]);
            $locationReportByDispatchRegisters = $locationReport->where('dispatch_register_id', '<>', null)->groupBy('dispatch_register_id');
            foreach ($locationReportByDispatchRegisters as $dispatchRegisterId => $locationReportByDispatchRegister) {
                $dispatchRegister = DispatchRegister::find($dispatchRegisterId);
                $route = $dispatchRegister->route;
                $mileageByRoutes->put(
                    $dispatchRegisterId,
                    (object)[
                        'route' => $route,
                        'dispatchRegister' => $dispatchRegister,
                        'mileage' => LocationReport::calculateMileageFromGroup($locationReportByDispatchRegister),
                    ]
                );
            }

            $reports->put(
                $vehicleId,
                (object)[
                    'vehicle' => $vehicle,
                    'mileage' => LocationReport::calculateMileageFromGroup($locationReport),
                    'byRoutes' => $mileageByRoutes,
                    'mileageByAllRoutes' => $mileageByRoutes->sum('mileage'),
                ]
            );
        }

        $mileageReport = (object)[
            'company' => $company,
            'dateReport' => $dateReport,
            'reports' => $reports,
            'mileageByFleet' => $reports->sum('mileage')
        ];

        return $mileageReport;
    }

    /**
     * @param $mileageReport
     */
    public function export($mileageReport)
    {
        $dateReport = $mileageReport->dateReport;
        $reports = $mileageReport->reports;
        Excel::create(__('Mileage Report') . " $dateReport", function ($excel) use ($reports, $dateReport) {
            foreach ($reports as $vehicleId => $report) {
                $vehicle = $report->vehicle;
                $reportByRoutes = $report->byRoutes;

                $dataExcel = array();
                foreach($reportByRoutes as $dispatchRegisterId => $reportByRoute){
                    $route = $reportByRoute->route;
                    $dispatchRegister = $reportByRoute->dispatchRegister;

                    $dataExcel[] = [
                        __('Route') => $route->name,                            # A CELL
                        __('Turn') => $dispatchRegister->turn,                  # B CELL
                        __('Round trip') => $dispatchRegister->round_trip,      # C CELL
                        __('Status') => $dispatchRegister->getStatusString(),   # D CELL
                        __('Mileage') => number_format($reportByRoute->mileage,2, ',', '')   # E CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => __('Mileage Report') . " $dateReport",
                    'title' => __('Mileage Report') . " $dateReport",
                    'subTitle' => __('Vehicle')." $vehicle->number:  ".number_format($report->mileage,2, ',', '')."Km ".__('in the day'),
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'mileageReport'
                ];

                /* SHEETS */
                $excel = PCWExporter::createHeaders($excel, $dataExport);
                $excel = PCWExporter::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('reports.route.off-road.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
