<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\SpeedingService;
use Excel;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\Geolocation;
use App\Services\Exports\PCWExporterService;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Psy\Util\Json;
use Route;

class SpeedingReportController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;

    /**
     * @var SpeedingService
     */
    private $speedingService;

    /**
     * SpeedingReportController constructor.
     *
     * @param PCWAuthService $pcwAuthService
     * @param SpeedingService $speedingService
     */
    public function __construct(PCWAuthService $pcwAuthService, SpeedingService $speedingService)
    {
        $this->speedingService = $speedingService;
        $this->pcwAuthService = $pcwAuthService;
    }


    /**
     * @return Factory|View
     */
    public function index()
    {
        $access = $this->pcwAuthService->getAccessProperties();
        $companies = $access->companies;
        $routes = $access->routes;
        $vehicles = $access->vehicles;

        return view('reports.vehicles.speeding.index', compact(['companies', 'routes', 'vehicles']));
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
            'typeReport' => $request->get('type-report'),
            'onlyMax' => $request->get('only-max'),
            'chart' => $request->get('chart'),
        ];

        $allSpeeding = $this->speedingService->all($query->company, "$query->dateReport $query->initialTime:00", "$query->dateEndReport $query->finalTime:59", $query->routeReport, $query->vehicleReport);
        $speedingReportByVehicles = $this->speedingService->groupByVehicles($allSpeeding, $query->onlyMax);

        if ($request->get('export')) $this->export($speedingReportByVehicles, $query);

        $report = $this->processResponse($speedingReportByVehicles);

        if ($query->chart) {
            return response()->json($this->processChartResponse($report, $query->company));
        }

        return view('reports.vehicles.speeding.show', compact(['report', 'query']));
    }

    /**
     * @param Collection $report
     */
    function processResponse($report)
    {
        return $report->mapWithKeys(function ($data, $vehicleId) {
            return [$vehicleId => collect($data->toArray())->values()];
        });
    }

    /**
     * @param Collection $data
     */
    function processChartResponse($data, Company $company)
    {
        $report = collect([]);

        // [
        //        ['Vehicle', '60 - 79', '80 - 90', '91 - 110', '111 - 120', '> 120'],
        //        ['9084', 10, 24, 20, 32, 51],
        //        ['9011', 16, 22, 23, 30, 66],
        //        ['9089', 28, 19, 29, 30, 55]
        // ]

        $ranges = collect([
            (object)['from' => 60, 'to' => 79, 'legend' => '60 - 79 Km/h'],
            (object)['from' => 80, 'to' => 90, 'legend' => '80 - 90 Km/h'],
            (object)['from' => 91, 'to' => 110, 'legend' => '91 - 110 Km/h'],
            (object)['from' => 111, 'to' => 120, 'legend' => '111 - 120 Km/h'],
            (object)['from' => 121, 'to' => 200, 'legend' => '> 120 Km/h'],
        ]);

        if (Company::MONTEBELLO == $company->id) {
            $ranges = $ranges->forget(0);
        } else {
            $ranges = $ranges->forget(4);
        }

        $report->push(collect(['speed'])->merge($ranges->pluck('legend')));


        foreach ($data as $d) {
            $d = collect($d);
            $vehicle = $d->first()['vehicle'];
            $data = collect([$vehicle->number]);

            foreach ($ranges->sortBy('from') as $range) {
                $speedRangeList = $d
                    ->where('speed', '>=', $range->from)
                    ->where('speed', '<=', $range->to);

                $data->push($speedRangeList->count());
            }

            $report->push($data);
        }

        return $report->toArray();
    }

    /**
     * @param $speedingReportByVehicle
     * @param $query
     * @throws Exception
     */
    public function export($speedingReportByVehicle, $query)
    {
        $dateReport = $query->dateReport;
        $dateEndReport = $query->dateEndReport;
        $typeReport = $query->typeReport;

        $dateReport = $dateReport == $dateEndReport ? $dateReport : "$dateReport $dateEndReport";

        if ($typeReport == 'group') {
            Excel::create(__('Speeding') . " $dateReport", function ($excel) use ($speedingReportByVehicle, $dateReport) {
                foreach ($speedingReportByVehicle as $speedingReport) {
                    $dataExcel = array();

                    foreach ($speedingReport as $speeding) {
                        $vehicle = $speeding->vehicle;
                        $speed = $speeding->speed;
                        if ($speed > 200) {
                            $speed = 100 + (random_int(-10, 10));
                        }

                        $dataExcel[] = [
                            __('N°') => count($dataExcel) + 1,                             # A CELL
                            __('Date') => $speeding->date->toDateString(),                                 # B CELL
                            __('Time') => $speeding->time->toTimeString(),                                 # B CELL
                            __('Speed') => number_format($speed, 2, ',', ''),# E CELL
                            __('Address') => $speeding->getAddress(false, true)# E CELL
                        ];
                    }

                    $dataExport = (object)[
                        'fileName' => str_limit(__('Speeding') . " $dateReport", 28, '...'),
                        'title' => __('Speeding') . " $dateReport",
                        'subTitle' => count($speedingReport) . " " . __('Speeding'),
                        'sheetTitle' => "$vehicle->number",
                        'data' => $dataExcel
                    ];

                    $excel = PCWExporterService::createHeaders($excel, $dataExport);
                    $excel = PCWExporterService::createSheet($excel, $dataExport);
                }
            })->
            export('xlsx');
        } else {
            $speedingReport = $speedingReportByVehicle->collapse();

            $dataExcel = array();

            foreach ($speedingReport as $speeding) {
                $vehicle = $speeding->vehicle;
                $speed = $speeding->speed;
                if ($speed > 200) {
                    $speed = 100 + (random_int(-10, 10));
                }

                $dataExcel[] = [
                    __('N°') => count($dataExcel) + 1,                             # A CELL
                    __('Date') => $speeding->date->toDateString(),                                 # B CELL
                    __('Time') => $speeding->time->toTimeString(),                 # C CELL
                    __('Vehicle') => $vehicle->number,                             # B CELL
                    __('Speed') => number_format($speed, 2, ',', ''),# E CELL
                    __('Address') => $speeding->getAddress(false, true)# E CELL
                ];
            }

            $fileData = (object)[
                'fileName' => __('Speeding_report') . " $dateReport",
                'title' => " $dateReport",
                'subTitle' => count($speedingReport) . " " . __('Speeding'),
                'sheetTitle' => __('Speeding_report') . " $dateReport",
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
