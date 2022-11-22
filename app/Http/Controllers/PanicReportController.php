<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\PanicService;
use Excel;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\Geolocation;
use App\Services\Exports\PCWExporterService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PanicReportController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;

    /**
     * @var PanicService
     */
    private $panicService;

    /**
     *
     * @param PCWAuthService $pcwAuthService
     * @param PanicService $panicService
     */
    public function __construct(PCWAuthService $pcwAuthService, PanicService $panicService)
    {
        $this->panicService = $panicService;
        $this->pcwAuthService = $pcwAuthService;
    }

    public function index()
    {
        $access = $this->pcwAuthService->getAccessProperties();
        $companies = $access->companies;
        $routes = $access->routes;
        $vehicles = $access->vehicles;

        if ($access->company->id === Company::COODETRANS || Auth::user()->isAdmin()) {
            return view('reports.vehicles.panic.index', compact(['companies', 'routes', 'vehicles']));
        }
        abort(403);
    }

    /**
     * @param Request $request
     * @return Factory|View|JsonResponse
     * @throws Exception
     */
    public function show(Request $request)
    {
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $date = $request->get('date-report');
        $dateEnd = $request->get('with-end-date') ? $request->get('date-end-report') : $date;

        $query = (object)[
            'stringParams' => explode('?', $request->getRequestUri())[1] ?? '',
            'company' => $this->pcwAuthService->getCompanyFromRequest($request),
            'dateReport' => $date,
            'dateEndReport' => $dateEnd,
            'routeReport' => $request->get('route-report'),
            'vehicleReport' => $request->get('vehicle-report'),
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'typeReport' => $request->get('type-report')
        ];

        $allPanic = $this->panicService->all($query->company, "$query->dateReport $query->initialTime:00", "$query->dateEndReport $query->finalTime:59", $query->routeReport, $query->vehicleReport);
        $panicReportByVehicles = $this->panicService->groupByVehicles($allPanic);

        if ($request->get('export')) $this->export($panicReportByVehicles, $query);

        $report = $this->processResponse($panicReportByVehicles);

        return view('reports.vehicles.panic.show', compact(['report', 'query']));
    }

    function processResponse(Collection $report)
    {
        return $report->mapWithKeys(function ($data, $vehicleId) {
            $report = collect([]);
            foreach ($data as $r) {
                $report->push((object)[
                    'id' => $r->id,
                    'date' => $r->date->toDateTimeString(),
                    'speed' => $r->speed,
                    'dispatchRegister' => $r->dispatchRegister ? $r->dispatchRegister->getRouteFields(true) : null,
                    'vehicle' => $r->vehicle->getAPIFields(null, true)
                ]);
            }

            return [$vehicleId => $report];
        });
    }

    /**
     * @param $reportByVehicle
     * @param $query
     * @throws Exception
     */
    public function export($reportByVehicle, $query)
    {
        $dateReport = $query->dateReport;
        $dateEndReport = $query->dateEndReport;
        $typeReport = $query->typeReport;

        $dateReport = $dateReport == $dateEndReport ? $dateReport : "$dateReport $dateEndReport";

        if ($typeReport == 'group') {
            Excel::create(__('Panic') . " $dateReport", function ($excel) use ($reportByVehicle, $dateReport) {
                foreach ($reportByVehicle as $report) {
                    $dataExcel = array();
                    $vehicle = (object)['number' => ''];

                    foreach ($report as $event) {
                        $vehicle = $event->vehicle;

                        $dataExcel[] = [
                            __('N°') => count($dataExcel) + 1,                         # A CELL
                            __('Date') => $event->date->toDateString(),                # B CELL
                            __('Time') => $event->time->toTimeString(),                # C CELL
                            __('Address') => $event->getAddress(false, true)           # D CELL
                        ];
                    }

                    $dataExport = (object)[
                        'fileName' => str_limit(__('Panic') . " $dateReport", 28),
                        'title' => __('Panic') . " $dateReport",
                        'subTitle' => count($report) . " " . __('Panic'),
                        'sheetTitle' => "$vehicle->number",
                        'data' => $dataExcel
                    ];

                    $excel = PCWExporterService::createHeaders($excel, $dataExport);
                    $excel = PCWExporterService::createSheet($excel, $dataExport);
                }
            })->
            export('xlsx');
        } else {
            $report = $reportByVehicle->collapse();

            $dataExcel = array();

            foreach ($report as $event) {
                $vehicle = $event->vehicle;

                $dataExcel[] = [
                    __('N°') => count($dataExcel) + 1,                          # A CELL
                    __('Date') => $event->date->toDateString(),                 # B CELL
                    __('Time') => $event->time->toTimeString(),                 # C CELL
                    __('Vehicle') => $vehicle->number,                          # D CELL
                    __('Address') => $event->getAddress(false, true)            # E CELL
                ];
            }

            $fileData = (object)[
                'fileName' => __('Panic_report') . " $dateReport",
                'title' => " $dateReport",
                'subTitle' => count($report) . " " . __('Panic'),
                'sheetTitle' => __('Panic_report') . " $dateReport",
                'data' => $dataExcel
            ];

            PCWExporterService::excel($fileData);
        }
    }

    /**
     * @param Location $location
     * @return mixed
     */
    public function getAddressFromCoordinates(Location $location)
    {
        return $location->getAddress(false, true);
    }

    /**
     * @param Location $location
     * @return mixed
     */
    public function getImageLocationFromCoordinates(Location $location)
    {
        return Geolocation::getImageLocationFromCoordinates($location->latitude, $location->longitude);
    }
}
