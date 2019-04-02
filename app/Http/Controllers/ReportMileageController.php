<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Models\Routes\Route;
use App\Services\PCWExporterService;
use App\Models\Vehicles\Vehicle;
use Auth;
use Excel;
use Illuminate\Http\Request;

class ReportMileageController extends Controller
{

    /**
     * @var GeneralController
     */
    private $generalController;

    public function __construct(GeneralController $generalController)
    {
        $this->generalController = $generalController;
    }

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
        $company = $this->generalController->getCompany($request);
        $dateReport = $request->get('date-report');

        $mileageReport = $this->buildMileageReport($company, $dateReport);

        if ($request->get('export')) $this->export($mileageReport);

        return view('reports.vehicles.mileage.show', compact(['mileageReport', 'stringParams']));
    }

    public function buildMileageReport(Company $company = null, $dateReport)
    {
        $vehicles = $company->vehicles;

        //$locations = Location::where('dispatch_register_id', '<>', null)
        $locations = Location::whereBetween('date', ["$dateReport 00:00:00", "$dateReport 23:59:59"])
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->orderBy('date')
            ->get();

        $locationsByVehicles = $locations->groupBy('vehicle_id');
        $reports = collect([]);
        foreach ($locationsByVehicles as $vehicleId => $locationsByVehicle) {
            $vehicle = Vehicle::find($vehicleId);

            $mileageByRoutes = collect([]);
            $locationsByDispatchRegisters = $locationsByVehicle
                ->where('dispatch_register_id', '<>', null)
                ->groupBy('dispatch_register_id');

            foreach ($locationsByDispatchRegisters as $dispatchRegisterId => $locationsByDispatchRegister) {
                $dispatchRegister = DispatchRegister::find($dispatchRegisterId);
                $route = $dispatchRegister->route;
                $mileageByRoutes->put(
                    $dispatchRegisterId,
                    (object)[
                        'route' => $route,
                        'dispatchRegister' => $dispatchRegister,
                        'mileage' => self::calculateMileageFromGroup($locationsByDispatchRegister),
                    ]
                );
            }

            $reports->put(
                $vehicleId,
                (object)[
                    'vehicle' => $vehicle,
                    'mileage' => self::calculateMileageFromGroup($locationsByVehicle),
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
                $typeReport = '';
                if(! count($dataExcel) ){
                    $dataExcel[] = [
                        __('Route') => __('No dispatch registers found'),
                        __('Mileage') => number_format($report->mileage,2, ',', '')
                    ];
                }else{
                    $typeReport = 'mileageReport';
                }

                $dataExport = (object)[
                    'fileName' => __('Mileage Report') . " $dateReport",
                    'title' => __('Mileage Report') . " $dateReport",
                    'subTitle' => __('Vehicle')." $vehicle->number:  ".number_format($report->mileage,2, ',', '')."Km ".__('in the day'),
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => $typeReport
                ];


                /* SHEETS */
                $excel = PCWExporterService::createHeaders($excel, $dataExport);
                $excel = PCWExporterService::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
    }

    private static function calculateMileageFromGroup($locationReport)
    {
        $firstLocation =  $locationReport->first();
        $lastLocation =  $locationReport->last();
        $totalKm = ($lastLocation->odometer - $firstLocation->odometer)/1000;

        return $totalKm;
    }
}
