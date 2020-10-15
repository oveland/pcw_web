<?php

namespace App\Http\Controllers;

use App\Models\Vehicles\Location;
use App\Models\Vehicles\LocationToday;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportRouteHistoricController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;

    public function __construct(PCWAuthService $pcwAuthService)
    {
        $this->pcwAuthService = $pcwAuthService;
    }

    /**
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $access = $this->pcwAuthService->getAccessProperties();
        $companies = $access->companies;
        $routes = $access->routes;
        $vehicles = $access->vehicles;

        $dateReport = $request->get('d');
        $vehicleReport = $request->get('v');
        $companyReport = $request->get('c');
        $initialTime = $request->get('i');
        $finalTime = $request->get('f');
        $hideMenu = session('hide-menu');

        return view('reports.route.historic.index', compact(['companies', 'routes', 'vehicles', 'dateReport', 'vehicleReport', 'companyReport', 'initialTime', 'finalTime', 'hideMenu']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $vehicleReport = $request->get('vehicle-report');
        $forExport = $request->get('export');
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $report = $this->buildHistoric($dateReport, $vehicleReport, $initialTime, $finalTime, $forExport);

        if ($forExport) $this->export($report);
        $report->exportLink = $request->getRequestUri() . "&export=true";

        return response()->json($report);
    }

    /**
     * @param $dateReport
     * @param $vehicleReport
     * @param $initialTime
     * @param $finalTime
     * @param bool $withAddress
     * @return object
     */
    public function buildHistoric($dateReport, $vehicleReport, $initialTime, $finalTime, $withAddress = false)
    {
        $vehicle = Vehicle::find($vehicleReport);

        $locations = Location::forDate($dateReport)->whereBetween('date', ["$dateReport $initialTime", "$dateReport $finalTime"])
            ->where('vehicle_id', $vehicleReport)
            ->with(['vehicle', 'dispatchRegister', 'vehicleStatus'])
            ->orderBy('date');

        $locations = $locations->get();

        $dataLocations = collect([]);

        $lastLocation = $locations->first();

        foreach ($locations as $location) {
            $dispatchRegister = $location->dispatchRegister;

            $period = '';
            $averagePeriod = '';
            if (Auth::user()->isAdmin()) {
                $period = $location->date->diffInSeconds($lastLocation->date);
                //$averagePeriod = intval($dataLocations->average('period')); // CAUTION this line take some long time!
                $averagePeriod = "--";
            }

            $dataLocations->push((object)[
                'time' => $location->date->format('H:i:s'),
                'period' => $period,
                'averagePeriod' => $averagePeriod,
                'date' => $location->date->format('Y-m-d'),
                'currentMileage' => number_format(intval($location->current_mileage) / 1000, 2, '.', ''),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'address' => $location->getAddress($withAddress),
                'odometer' => $location->odometer,
                'orientation' => $location->orientation,
                'speed' => $location->speed,
                'speeding' => $location->speeding,
                'offRoad' => $location->off_road,
                'routeDistance' => number_format(intval($location->distance) / 1000, 2, '.', ''),
                'vehicleStatus' => (object)[
                    'id' => $location->vehicleStatus->id,
                    'status' => $location->vehicleStatus->des_status,
                    'iconClass' => $location->vehicleStatus->icon_class,
                    'mainClass' => $location->vehicleStatus->main_class,
                ],
                'dispatchRegister' => $dispatchRegister ? $dispatchRegister->getAPIFields() : null,
                'vehicle' => $location->vehicle->getAPIFields()
            ]);
            $lastLocation = $location;
        }

        $totalLocations = $dataLocations->count();

        $report = (object)[
            'dateReport' => $dateReport,
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'vehicle' => $vehicle->getAPIFields(),
            'historic' => $dataLocations,
            'total' => $totalLocations,
            'from' => $totalLocations ? $dataLocations->first()->time : '--:--',
            'to' => $totalLocations ? $dataLocations->last()->time : '--:--',
        ];

        return $report;
    }

    /**
     * @param $report
     * @throws Exception
     */
    public function export($report)
    {
        $dataExcel = array();
        foreach ($report->historic as $location) {
            $infoRoute = $this->getInfoRoute($location);

            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                                                  # A CELL
                __('Time') => $location->time,                                                                      # B CELL
                __('Mileage') => $location->currentMileage,                                                         # C CELL
                __('Speed') => number_format($location->speed, 2, ',', ''),         # D CELL
                __('Exc.') => $location->speeding ? __('YES') : __('NO'),                        # E CELL
                __('Vehicle status') => $location->vehicleStatus ? $location->vehicleStatus->status : '...',                                           # F CELL
                __('Address') => $location->address,                                                                # G CELL
                __('Info route') => $infoRoute                                                                      # H CELL
            ];
        }

        $fileData = (object)[
            'fileName' => __('Historic') . " " . $report->vehicle->number . " $report->dateReport",
            'title' => __('Historic') . " $report->dateReport - #" . $report->vehicle->number,
            'subTitle' => __('Time') . " $report->initialTime - $report->finalTime ",
            'sheetTitle' => __('Historic') . " " . $report->vehicle->number,
            'data' => $dataExcel,
            'type' => 'historicRouteReport'
        ];
        //foreach ()
        /* SHEETS */

        PCWExporterService::excel($fileData);

    }

    /**
     * @param $reportLocation
     * @return string
     */
    public function getInfoRoute($reportLocation)
    {
        $infoDispatchRegister = "";
        $dispatchRegister = $reportLocation->dispatchRegister;

        if ($dispatchRegister) {
            $route = $dispatchRegister->route;
            $infoDispatchRegister = "$route->name \n " . __('Round trip') . " $dispatchRegister->round_trip \n " . __('Turn') . " $dispatchRegister->turn \n " . __('Dispatched') . " $dispatchRegister->departure_time \n " . __('Driver') . " $dispatchRegister->driver_name";
        }

        return $infoDispatchRegister;
    }
}
