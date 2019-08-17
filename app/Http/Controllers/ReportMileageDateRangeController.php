<?php

namespace App\Http\Controllers;

use App\Exports\MileageDateRangeExport;
use App\LastLocation;
use App\Models\Company\Company;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use App\Services\PCWTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportMileageDateRangeController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;
    /**
     * @var PCWExporterService
     */
    private $pcwExporterService;

    public function __construct(PCWAuthService $pcwAuthService, PCWExporterService $pcwExporterService)
    {
        $this->pcwAuthService = $pcwAuthService;
        $this->pcwExporterService = $pcwExporterService;
    }

    /**
     * @return View
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
     * @return View | MileageDateRangeExport
     */
    public function show(Request $request)
    {
        $company = $this->pcwAuthService->getCompanyFromRequest($request);
        $initialDateReport = $request->get('initial-date-report');
        $finalDateReport = $request->get('final-date-report');
        $vehicleReport = $request->get('vehicle-report');

        if( $initialDateReport >= Carbon::now()->toDateString() || $finalDateReport >= Carbon::now()->toDateString() ){
            return view('partials.dates.dateOnlyBackToCurrent');
        }

        $mileageReport = $this->buildMileageReport($company, $vehicleReport, $initialDateReport, $finalDateReport);

        if ($request->get('export')) return $this->pcwExporterService->exportMileageDateRange($mileageReport);

        return view('reports.vehicles.mileage.dates.show', compact(['mileageReport']));
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
        if ($vehicleReport != 'all') $vehicles = $vehicles->where('id', $vehicleReport);

        $reports = collect([]);
        $lastLocations = LastLocation::whereBetween('date', ["$initialDateReport 00:00:00", "$finalDateReport 23:59:59"])
            //->with('reportVehicleStatus')
            ->whereIn('vehicle_id', $vehicles->pluck('id'))->get();

        $lastLocationsByVehicles = $lastLocations->groupBy(function($ll){
            return $ll->vehicle_id;
        });

        $initialDate = Carbon::createFromFormat('Y-m-d', $initialDateReport);
        $finalDate = Carbon::createFromFormat('Y-m-d', $finalDateReport);

        $dateRange = PCWTime::dateRange($initialDate, $finalDate);

        foreach ($vehicles as $vehicle) {
            foreach ($dateRange as $date) {
                $date = $date->toDateString();
                $key = ($vehicle->active ? 'A' : 'B') . "$vehicle->id $date";

                $lastLocation = isset($lastLocationsByVehicles[$vehicle->id]) ? $lastLocationsByVehicles[$vehicle->id]->filter(function ($ll) use ($date, $vehicle) {
                    return $ll->date->toDateString() == $date;
                })->first() : null;

                $reports->put($key,
                    (object)[
                        'key' => $key,
                        'vehicleId' => $vehicle->id,
                        'vehiclePlate' => $vehicle->plate,
                        'vehicleNumber' => $vehicle->number,
                        'vehicleIsActive' => $lastLocation ? $lastLocation->vehicle_active : false,
                        'vehicleStatus' => $lastLocation ? ($lastLocation->vehicle_active ? __('Active'):__('Inactive')) : __('No GPS reports found'),
                        'reportVehicleStatus' => $lastLocation ? $lastLocation->reportVehicleStatus : null,
                        'date' => $date,
                        'mileage' => $lastLocation ? $lastLocation->current_mileage : 0,
                        'hasReports' => !!$lastLocation,
                    ]
                );
            }
        }

        $reports = $reports->sortBy('key');

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
}