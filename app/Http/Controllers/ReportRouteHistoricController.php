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
            ->with(['vehicle', 'dispatchRegister', 'dispatchRegister.driver', 'vehicleStatus', 'passenger', 'photo'])
            ->orderBy('date');
        $locations = $locations->get();

        $dataLocations = collect([]);

        $lastLocation = $locations->first();

        $totalPassengers = 0;
        $prevTotalPassengers = 0;

        $totalCharge = 0;
        $tariff = 0;
        $tariffCharges = [];

        $totalPassengersOnPhoto = 0;
        $passengersTripOnPhoto = 0;
        $totalInRoundTrips = 0;
        $passengersInRoundTrip = 0;
        $passengersOutRoundTrip = 0;

        $totalAscents = 0;
        $totalAscentsInRoundTrip = 0;
        $prevTotalAscentsInRoundTrip = 0;

        $totalDescents = 0;
        $totalDescentsInRoundTrip = 0;
        $prevTotalDescentsInRoundTrip = 0;

        $frameCounter = '';
        $trips = [];

        $photoId = null;
        $prevDr = null;
        $newTurn = true;

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

            if ($dispatchRegister) {
                $newTurn = $prevDr ? $dispatchRegister->id != $prevDr->id : true;
                $prevDr = $dispatchRegister;
            }

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

                $totalCharge = $passenger->total_charge;
                $tariff = $passenger->tariff;

                if ($dispatchRegister) {
                    if ($passengersInRoundTrip <= $passenger->in_round_trip || $newTurn) {
                        $passengersInRoundTrip = $passenger->in_round_trip;
                    }

                    if ($passengersOutRoundTrip <= $passenger->out_round_trip || $newTurn) {
                        $passengersOutRoundTrip = $passenger->out_round_trip;
                    }

                    if ($totalAscentsInRoundTrip <= $passenger->ascents_in_round_trip || $newTurn) {
                        $totalAscentsInRoundTrip = $passenger->ascents_in_round_trip;
                    }

                    if ($totalDescentsInRoundTrip <= $passenger->descents_in_round_trip || $newTurn) {
                        $totalDescentsInRoundTrip = $passenger->descents_in_round_trip;
                    }


                    $trips[$dispatchRegister->id] = (object)[
                        "index" => $index,
                        "routeName" => $dispatchRegister->route->name,
                        "roundTrip" => $dispatchRegister->round_trip,
                        "departureTime" => $dispatchRegister->departure_time,
                        "passengers" => (object)[
                            "total" => $totalPassengers,
                            "inRoundTrip" => $passengersInRoundTrip,

                            'counted' => $passenger->counted,
                            'tariff' => $tariff,
                            'charge' => $passenger->charge,
                            'totalCharge' => $totalCharge,
                        ],
                    ];

                    $totalInRoundTrips = collect($trips)->sum(function ($t) {
                        return $t->passengers->inRoundTrip;
                    });

                    if ($tariff) {
                        $counted = ($totalPassengers - $prevTotalPassengers);
                        $tariffCharges[$tariff] = (object)[
                            'tariff' => $tariff,
                            'charge' => $passenger->charge,
                            'counted' => $counted,
                            'totalCounted' => (isset($tariffCharges[$tariff]) ? $tariffCharges[$tariff]->totalCounted : 0) + $counted,
                            'totalCharge' => (isset($tariffCharges[$tariff]) ? $tariffCharges[$tariff]->totalCharge : 0) + ($counted * $tariff),
                        ];
                    }
                } else {
                    $passengersInRoundTrip = 0;
                    $passengersOutRoundTrip = 0;

                    $totalAscentsInRoundTrip = 0;
                    $totalDescentsInRoundTrip = 0;
                }
            } else {
                if ($newTurn) {
                    $passengersInRoundTrip = 0;
                    $passengersOutRoundTrip = 0;

                    $totalAscentsInRoundTrip = 0;
                    $totalDescentsInRoundTrip = 0;
                }

                if ($dispatchRegister) {
                    $trips[$dispatchRegister->id] = (object)[
                        "index" => $index,
                        "routeName" => $dispatchRegister->route->name,
                        "roundTrip" => $dispatchRegister->round_trip,
                        "departureTime" => $dispatchRegister->departure_time,
                        "passengers" => (object)[
                            "total" => $totalPassengers,
                            "inRoundTrip" => $passengersInRoundTrip,

                            'counted' => 0,
                            'tariff' => $tariff,
                            'charge' => 0,
                            'totalCharge' => $totalCharge,
                        ]
                    ];
                }
            }

//            $countedAscents = $prevTotalAscentsInRoundTrip !== $totalAscentsInRoundTrip;
//            $countedDescents = $prevTotalDescentsInRoundTrip !== $totalDescentsInRoundTrip;

            $countedAscents = $prevTotalPassengers !== $totalPassengers;
            $countedDescents = false;

            if ($location->photo) {
                $photoId = $location->photo->id;
                $totalPassengersOnPhoto = $totalPassengers;
                $passengersTripOnPhoto = $passengersInRoundTrip;
            }

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
                'dispatchRegister' => $dispatchRegister ? $dispatchRegister->getRouteFields() : null,
                'vehicle' => $location->vehicle->getAPIFields(null, true),
                'passengers' => (object)[
                    'total' => $totalPassengers,
                    'totalInRoundTrips' => $totalInRoundTrips,
                    'inRoundTrip' => $passengersInRoundTrip,
                    'outRoundTrip' => $passengersOutRoundTrip,
                    'totalAscents' => $totalAscents,
                    'totalDescents' => $totalDescents > $totalAscents ? $totalAscents : $totalDescents,
                    'ascentsInRoundTrip' => $totalAscentsInRoundTrip,
                    'descentsInRoundTrip' => $totalDescentsInRoundTrip > $totalAscentsInRoundTrip ? $totalAscentsInRoundTrip : $totalDescentsInRoundTrip,
                    'counted' => ($countedAscents || $countedDescents),
                    'countedAscents' => $countedAscents,
                    'countedDescents' => $countedDescents,
                    'frame' => $frameCounter,
                    'trips' => $trips,

                    'tariff' => $tariff,
                    'totalCharge' => $totalCharge,
                    'tariffCharges' => $tariffCharges,
                ],
                'photo' => (object)[
                    'id' => $photoId,
                    'index' => $index,
                    'passengers' => $totalPassengersOnPhoto,
                    'passengersTrip' => $passengersTripOnPhoto,
                ]
            ]);

            $prevTotalAscentsInRoundTrip = $totalAscentsInRoundTrip;
            $prevTotalDescentsInRoundTrip = $totalDescentsInRoundTrip;
            $prevTotalPassengers = $totalPassengers;
            $lastLocation = $location;
        }

        $totalLocations = $dataLocations->count();

        $report = (object)[
            'dateReport' => $dateReport,
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'vehicle' => $vehicle->getAPIFields(null, true),
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
