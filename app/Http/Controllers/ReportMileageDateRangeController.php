<?php

namespace App\Http\Controllers;

use App\LastLocation;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use App\Models\Vehicles\Vehicle;
use Excel;
use Illuminate\Http\Request;

class ReportMileageDateRangeController extends Controller
{
    /**
     * @var GeneralController
     */
    private $generalController;
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;

    public function __construct(PCWAuthService $pcwAuthService, GeneralController $generalController)
    {
        $this->generalController = $generalController;
        $this->pcwAuthService = $pcwAuthService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $accessProperties = $this->pcwAuthService->getAccessProperties();
        $companies = $accessProperties->companies;
        $vehicles = $accessProperties->vehicles;
        return view('reports.vehicles.mileage.dates.index', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $initialDateReport = $request->get('initial-date-report');
        $finalDateReport = $request->get('final-date-report');
        $vehicleReport = $request->get('vehicle-report');

        $mileageReport = $this->buildMileageReport($company, $vehicleReport, $initialDateReport, $finalDateReport);

        if ($request->get('export')) $this->export($mileageReport);

        return view('reports.vehicles.mileage.dates.show', compact(['mileageReport', 'stringParams']));
    }

    /**
     * @param Company $company
     * @param string $vehicleReport
     * @param $initialDateReport
     * @param $finalDateReport
     * @return object
     */
    public function buildMileageReport(Company $company, $vehicleReport, $initialDateReport, $finalDateReport)
    {
        $vehicles = $company->vehicles;
        if($vehicleReport != 'all')$vehicles = $vehicles->where('id', $vehicleReport);

        $lastLocations = LastLocation::whereBetween('date', ["$initialDateReport 00:00:00", "$finalDateReport 23:59:59"])
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->orderBy('vehicle_id')
            ->orderBy('date')
            ->get();

        $reports = collect([]);
        foreach ($lastLocations as $lastLocation) {
            $vehicle = $lastLocation->vehicle;

            $reports->push(
                (object)[
                    'vehicleId' => $vehicle->id,
                    'vehiclePlate' => $vehicle->plate,
                    'vehicleNumber' => $vehicle->number,
                    'date' => $lastLocation->date->toDateString(),
                    'mileage' => $lastLocation->current_mileage
                ]
            );
        }

        $mileageReport = (object)[
            'companyReport' => $company->id,
            'vehicleReport' => $vehicleReport,
            'initialDateReport' => $initialDateReport,
            'finalDateReport' => $finalDateReport,
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

        $reports = $mileageReport->reports;
        $dataExcel = collect([]);
        foreach ($reports as $report) {
            $dataExcel->push([
                __('N°') => count($dataExcel) + 1,           # A CELL
                __('Date') => $report->date,         # B CELL
                __('Number') => $report->vehicleNumber,      # C CELL
                __('Plate') => $report->vehiclePlate,        # D CELL
                __('Mileage')." (Km)" => "=$report->mileage/1000",   # E CELL
            ]);
        }

        $vehicleNumber = __("for all");
        if($mileageReport->vehicleReport != 'all' && $dataExcel->count()){
            $vehicleNumber = __('Vehicle')." ".$dataExcel->first()[__('Number')];
        }

        $fileData = [
            'fileName' => __('Mileage') . " $mileageReport->initialDateReport $mileageReport->finalDateReport",
            'title' => __('Mileage') . " $mileageReport->initialDateReport $mileageReport->finalDateReport",
            'subTitle' => __('Mileage')." $vehicleNumber",
            'data' => $dataExcel->toArray(),
            'type' => 'reportMileageDateRange'
        ];

        PCWExporterService::excel($fileData);
    }

    private static function calculateMileageFromGroup($locationReport)
    {
        $firstLocation =  $locationReport->first();
        $lastLocation =  $locationReport->last();
        $totalKm = ($lastLocation->odometer - $firstLocation->odometer)/1000;

        return $totalKm;
    }
}