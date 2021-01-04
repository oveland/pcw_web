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
            ->with(['vehicle', 'dispatchRegister', 'vehicleStatus', 'passenger'])
            ->orderBy('date');
        $locations = $locations->get();

        $dataLocations = collect([]);

        $lastLocation = $locations->first();

        $totalPassengers = 0;
        $totalPassengersInRoundTrip = 0;
        $totalPassengersOutRoundTrip = 0;
        $prevTotalPassengers = 0;

        $totalAscents = 0;
        $totalAscentsInRoundTrip = 0;
        $prevTotalAscentsInRoundTrip = 0;

        $totalDescents = 0;
        $totalDescentsInRoundTrip = 0;
        $prevTotalDescentsInRoundTrip = 0;

        $frameCounter = '';
        $trips = [];

        foreach ($locations as $index => $location) {
            $dispatchRegister = $location->dispatchRegister;

            $period = '';
            $averagePeriod = '';
            if (Auth::user()->isAdmin()) {
                $period = $location->date->diffInSeconds($lastLocation->date);
                //$averagePeriod = intval($dataLocations->average('period')); // CAUTION this line take some long time!
                $averagePeriod = "--";
            }

            $passenger = $location->passenger;

            if ($passenger) {
                $frameCounter = $passenger->frame ? $passenger->date->toTimeString() . " • " . $passenger->frame : $frameCounter;

                if ($totalPassengers <= $passenger->total) {
                    $totalPassengers = $passenger->total;
                }

                if ($totalAscents <= $passenger->total_ascents) {
                    $totalAscents = $passenger->total_ascents;
                }

                if ($totalDescents <= $passenger->total_descents) {
                    $totalDescents = $passenger->total_descents;
                }

                if ($dispatchRegister) {
                    if ($totalPassengersInRoundTrip <= $passenger->in_round_trip) {
                        $totalPassengersInRoundTrip = $passenger->in_round_trip;
                    }

                    if ($totalPassengersOutRoundTrip <= $passenger->out_round_trip) {
                        $totalPassengersOutRoundTrip = $passenger->out_round_trip;
                    }

                    if ($totalAscentsInRoundTrip <= $passenger->ascents_in_round_trip) {
                        $totalAscentsInRoundTrip = $passenger->ascents_in_round_trip;
                    }

                    if ($totalDescentsInRoundTrip <= $passenger->descents_in_round_trip) {
                        $totalDescentsInRoundTrip = $passenger->descents_in_round_trip;
                    }

                    $trips[$dispatchRegister->id] = [
                        "index" => $index,
                        "routeName" => $dispatchRegister->route->name,
                        "roundTrip" => $dispatchRegister->round_trip,
                        "departureTime" => $dispatchRegister->departure_time,
                        "passengers" => [
                            "total" => $totalPassengers,
                            "inRoundTrip" => $totalPassengersInRoundTrip,
                        ]
                    ];
                } else {
                    $totalPassengersInRoundTrip = 0;
                    $totalPassengersOutRoundTrip = 0;

                    $totalAscentsInRoundTrip = 0;
                    $totalDescentsInRoundTrip = 0;
                }
            }

            $countedAscents = $prevTotalAscentsInRoundTrip !== $totalAscentsInRoundTrip;
            $countedDescents = $prevTotalDescentsInRoundTrip !== $totalDescentsInRoundTrip;

            $dataLocations->push((object)[
                'time' => $location->date->format('H:i:s'),
                'period' => $period,
                'averagePeriod' => $averagePeriod,
                'date' => $location->date->format('Y-m-d'),
                'currentMileage' => number_format(intval($location->current_mileage) / 1000, 2, '.', ''),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
//                'address' => $location->getAddress($withAddress),
                'address' => "",
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
                'vehicle' => $location->vehicle->getAPIFields(),
                'passengers' => (object)[
                    'total' => $totalPassengers,
                    'inRoundTrip' => $totalPassengersInRoundTrip,
                    'outRoundTrip' => $totalPassengersOutRoundTrip,
                    'totalAscents' => $totalAscents,
                    'totalDescents' => $totalDescents > $totalAscents ? $totalAscents : $totalDescents,
                    'ascentsInRoundTrip' => $totalAscentsInRoundTrip,
                    'descentsInRoundTrip' => $totalDescentsInRoundTrip > $totalAscentsInRoundTrip ? $totalAscentsInRoundTrip : $totalDescentsInRoundTrip,
                    'counted' => ($countedAscents || $countedDescents),
                    'countedAscents' => $countedAscents,
                    'countedDescents' => $countedDescents,
                    'frame' => $frameCounter,
                    'trips' => $trips
                ],
            ]);

            $prevTotalPassengers = $totalPassengers;
            $prevTotalAscentsInRoundTrip = $totalAscentsInRoundTrip;
            $prevTotalDescentsInRoundTrip = $totalDescentsInRoundTrip;
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
