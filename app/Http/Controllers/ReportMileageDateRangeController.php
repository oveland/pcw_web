<?php

namespace App\Http\Controllers;

use App\LastLocation;
use App\Models\Company\Company;
use App\Models\Vehicles\VehicleIssue;
use App\Models\Vehicles\VehicleIssueType;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use App\Services\PCWTime;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
     * @return Factory|View
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
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $initialDateReport = $request->get('initial-date-report');
        $finalDateReport = $request->get('final-date-report');
        $vehicleReport = $request->get('vehicle-report');

        if ($initialDateReport >= Carbon::now()->toDateString() || $finalDateReport >= Carbon::now()->toDateString()) {
            return view('partials.dates.dateOnlyBackToCurrent');
        }

        $mileageReport = $this->buildMileageReport($company, $vehicleReport, $initialDateReport, $finalDateReport);

        if ($request->get('export') == 'true') $this->export($mileageReport);

        return view('reports.vehicles.mileage.dates.show', compact(['mileageReport']));
    }

    /**
     * @param Company $company
     * @param $vehicleReport
     * @param $initialDateReport
     * @param $finalDateReport
     * @return object
     */
    public function buildMileageReport(Company $company, $vehicleReport, $initialDateReport, $finalDateReport)
    {
        $reports = collect([]);

        $vehicles = $company->vehicles();
        if (intval($vehicleReport)) $vehicles = $vehicles->where('id', $vehicleReport);
        else if ($vehicleReport != 'all' && in_array($vehicleReport, ['yb', 'coop'])) {
            $vehicles = $vehicles->where('tags', 'like', "%$vehicleReport%");
        }
        $vehicles = $vehicles->get();

        $lastLocationsByVehicles = LastLocation::whereBetween('date', ["$initialDateReport 00:00:00", "$finalDateReport 23:59:59"])
            ->with('reportVehicleStatus')
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->get()
            ->groupBy('vehicle_id');

        $dateRange = PCWTime::dateRange(Carbon::createFromFormat('Y-m-d', $initialDateReport), Carbon::createFromFormat('Y-m-d', $finalDateReport));

        foreach ($vehicles as $vehicle) {
            foreach ($dateRange as $date) {
                $date = $date->toDateString();
                $key = ($vehicle->active ? 'A' : 'B') . str_pad($vehicle->number, 5, '0', STR_PAD_LEFT) . "-$date";

                $lastLocation = isset($lastLocationsByVehicles[$vehicle->id]) ? $lastLocationsByVehicles[$vehicle->id]->filter(function ($ll) use ($date, $vehicle) {
                    return $ll->date->toDateString() == $date;
                })->first() : null;

                $mileage = $lastLocation ? $lastLocation->current_mileage_odometer : 0;
                $mileageOdometer = $lastLocation ? $lastLocation->current_mileage : 0;
                $mileageRoute = $lastLocation ? $lastLocation->current_mileage_route : 0;

                $maxMileage = $company->getMaxDailyMileage();
                if ($mileageOdometer < 0 || $mileageOdometer > $maxMileage) $mileageOdometer = 0;
                if ($mileageRoute < 0) $mileageRoute = 0;
                if ($mileage < 0 || $mileage > $maxMileage) $mileage = 0;

                $observations = '';
                $vehicleIsActive = $lastLocation ? $lastLocation->vehicle_active : false;

                if ($lastLocation) {
                    $vehicleStatus = $lastLocation->vehicle_active ? __('Active') : __('Inactive');
                } else {
                    $vehicleIssue = VehicleIssue::where('vehicle_id', $vehicle->id)
                        ->whereDate('date', '<=', $date)
                        ->orderByDesc('id')
                        ->first();

                    if ($vehicleIssue) {
                        $vehicleIsActive = $vehicleIssue->issue_type_id == VehicleIssueType::OUT;
                        $vehicleStatus = ($vehicleIsActive ? __('Active') : __('Vehicle issue')) . " " . __('from') . " " . $vehicleIssue->date;

                        $u = $vehicleIssue->user;
                        if ($u) {
                            $vehicleStatus .= " • $u->username";
                        }

                        $observations = $vehicleIssue->observations;
                    } else {
                        $vehicleIsActive = false;
                        $vehicleStatus = __('Inactive');

                        $observations = __('No GPS reports found');
                    }
                }

                $reports->put($key,
                    (object)[
                        'key' => $key,
                        'vehicleId' => $vehicle->id,
                        'vehiclePlate' => $vehicle->plate,
                        'vehicleNumber' => $vehicle->number,
                        'vehicleIsActive' => $vehicleIsActive,
                        'vehicleStatus' => $vehicleStatus,
                        'observations' => $observations,
                        'reportVehicleStatus' => $lastLocation ? $lastLocation->reportVehicleStatus : null,
                        'date' => $date,
                        'mileage' => $mileage,
                        'mileageOdometer' => $mileageOdometer,
                        'mileageRoute' => $mileageRoute,
                        'hasReports' => !!$lastLocation,
                    ]
                );
            }
        }

        $reports = $reports->sortBy('key');

        return (object)[
            'companyReport' => $company->id,
            'vehicleReport' => $vehicleReport,
            'initialDateReport' => $initialDateReport,
            'finalDateReport' => $finalDateReport,
            'reports' => $reports,
            'mileageByFleet' => $reports->sum('mileage'),
            'mileageOdometerByFleet' => $reports->sum('mileageOdometer'),
            'mileageRouteByFleet' => $reports->sum('mileageRoute'),
        ];
    }

    /**
     * @param $mileageReport
     */
    public function export($mileageReport)
    {

        $reports = $mileageReport->reports;
        $dataExcel = collect([]);
        foreach ($reports as $report) {
            $mileage = $report->mileage ?? 0;
            $dataExcel->push([
                __('N°') => count($dataExcel) + 1,           # A CELL
                __('Date') => $report->date,                 # B CELL
                __('Number') => $report->vehicleNumber,      # C CELL
                __('Plate') => $report->vehiclePlate,        # D CELL
                __('Status') => $report->vehicleStatus,      # E CELL
                __('Mileage') . " (Km)" => "=$mileage/1000", # F CELL
            ]);
        }

        $vehicleNumber = __("all");
        if ($mileageReport->vehicleReport != 'all' && intval($mileageReport->vehicleReport) && $dataExcel->count()) {
            $vehicleNumber = __('Vehicle') . " " . $dataExcel->first()[__('Number')];
        }

        $fileData = [
            'fileName' => __('Mileage') . " $mileageReport->initialDateReport $mileageReport->finalDateReport",
            'title' => __('Mileage') . " $mileageReport->initialDateReport $mileageReport->finalDateReport",
            'subTitle' => __('Mileage') . " $vehicleNumber",
            'data' => $dataExcel->toArray(),
            'type' => 'reportMileageDateRange'
        ];

        PCWExporterService::excel($fileData);
    }
}