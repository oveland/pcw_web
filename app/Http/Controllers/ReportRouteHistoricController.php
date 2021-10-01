<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
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
use Illuminate\Support\Str;
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
        $vehicleReport = Vehicle::where('number', request()->get('n'))
            ->where('company_id', 14)->first();
        $vehicleReport = $vehicleReport ? $vehicleReport->id : null;

        $vehicleReport = $vehicleReport ? $vehicleReport : $request->get('v');
        $companyReport = $request->get('c');
        $initialTime = $request->get('i');
        $finalTime = $request->get('f');
        $speed = $request->get('s');
        $hideMenu = session('hide-menu');

        return view('reports.route.historic.index', compact(['companies', 'routes', 'vehicles', 'dateReport', 'vehicleReport', 'companyReport', 'initialTime', 'finalTime', 'speed', 'hideMenu']));
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
            ->with(['vehicle', 'dispatchRegister', 'dispatchRegister.driver', 'vehicleStatus', 'passenger', 'photo', 'photos'])
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
        $photoTags = [];
        $totalInRoundTrips = 0;
        $passengersInRoundTrip = 0;
        $passengersOutRoundTrip = 0;
        $seatingCounted = [];

        $totalAscents = 0;
        $totalAscentsInRoundTrip = 0;

        $totalDescents = 0;
        $totalDescentsInRoundTrip = 0;

        $countedAscents = false;
        $countedDescents = false;

        $frameCounter = '';
        $trips = [];

        $photoId = null;
        $photoTime = null;
        $prevDr = null;
        $newTurn = true;

        $photos = [];

        if ($dateReport < '2021-09-03') { // TODO: Delete on 2022
            $locations = $locations->filter(function (Location $l) {
                return !Str::contains($l->status, 'X');
            })->values();
        }

        foreach ($locations as $index => $location) {
            $dispatchRegister = $location->dispatchRegister;
            $inRoute = $dispatchRegister && $dispatchRegister->isActive();

            $period = '';
            $averagePeriod = '';
            if (Auth::user()->isAdmin()) {
                $period = $location->date->diffInSeconds($lastLocation->date);
                $averagePeriod = "--";
            }

            $passenger = $location->passenger;

            if ($passenger) {
//                $dispatchRegister = $passenger->dispatchRegister;

                if ($dispatchRegister) {
                    $newTurn = $prevDr ? $dispatchRegister->id != $prevDr->id : true;
                    $prevDr = $dispatchRegister;
                }

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

//                $totalCharge = $passenger->total_charge;
                $tariff = $passenger->tariff;

                if ($inRoute) {
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

                    $countedAscents = $passenger->ascents_in_round_trip ?? false;
//                    if ($passenger->counted) $countedAscents = false;
//                    $countedDescents = $passenger->descents_in_round_trip ?? false;
                    $countedDescents = false;

                    if ($tariff) {
                        $counted = ($totalPassengers - $prevTotalPassengers);
                        $charge = $counted * $tariff;
                        $totalChargeRoundTrip = (isset($tariffCharges[$tariff]) ? $tariffCharges[$tariff]->totalCharge : 0) + $charge;
                        $totalCountedRoundTrip = (isset($tariffCharges[$tariff]) ? $tariffCharges[$tariff]->totalCounted : 0) + $counted;

                        $tariffCharges[$tariff] = (object)[
                            'tariff' => $tariff,
                            'charge' => $charge,
                            'counted' => $counted,
                            'totalCounted' => $totalCountedRoundTrip,
                            'totalCharge' => $totalChargeRoundTrip,
                        ];

                        $totalCharge = $totalCharge + $charge;
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
                } else {
                    $passengersInRoundTrip = 0;
                    $passengersOutRoundTrip = 0;

                    $totalAscentsInRoundTrip = 0;
                    $totalDescentsInRoundTrip = 0;
                }
            } else {
                $countedAscents = false;
                $countedDescents = false;

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

//            $countedAscents = $prevTotalPassengers !== $totalPassengers;
//            $countedDescents = false;

            if ($location->photo) {
                $photoId = $location->photo->id;
                $photoTime = $location->photo->date->toTimeString();
                $totalPassengersOnPhoto = $totalPassengers;
                $passengersTripOnPhoto = $passengersInRoundTrip;
                $photoTags = $passenger->tags ?? [];
            }

            if (!$inRoute) {
                $passengersTripOnPhoto = 0;
                $passengersInRoundTrip = 0;
                $photoTags = [];
                $seatingCounted = [];
            }

            if ($location->photos->count()) {
                $photos = $location->photos->toArray();
            }

            $dataLocations->push((object)[
                'id' => $location->id,
                'inRoute' => $inRoute,
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
                    'counted' => ($countedAscents || $countedDescents || ($passenger->counted ?? false)),
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
                    'time' => $photoTime,
                    'index' => $index,
                    'passengers' => $totalPassengersOnPhoto,
                    'passengersTrip' => $passengersTripOnPhoto,
                    "events" => $this->processPhotoEvents($location->photo, $photoTags, $seatingCounted)
                ],
                'photos' => $photos
            ]);

            $prevTotalPassengers = $totalPassengers;
            $lastLocation = $location;
        }

        $totalLocations = $dataLocations->count();

        return (object)[
            'dateReport' => $dateReport,
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'vehicle' => $vehicle->getAPIFields(null, true),
            'historic' => $dataLocations,
            'total' => $totalLocations,
            'from' => $totalLocations ? $dataLocations->first()->time : '--:--',
            'to' => $totalLocations ? $dataLocations->last()->time : '--:--',
            'config' => [
                'events' => [
                    'panic' => $vehicle->company->id === Company::COODETRANS
                ]
            ]
        ];
    }

    function processPhotoEventTypes($photoAlerts)
    {
        $photoAlerts = collect($photoAlerts);
        $withEvents = $photoAlerts->where('total', '>', 0);

        $events = $withEvents->pluck('event');
        if ($withEvents->count() > 1) $events->push(3);

        return $events;
    }

    function processPhotoEvents($photo, $photoTags, &$seatingCounted)
    {
        $photoTags = collect($photoTags);
        $alerts = collect([]);
        $countedSeating = collect([]);

        if ($photoTags->get('occupation')) {
            $alerts->push([
                'color' => 'primary',
                'message' => "<h5>" . strtoupper(__('Profile seating')) . "</h5>"
            ]);

            $occupation = collect($photoTags->get('occupation'));
            $percent = $occupation->get('percent');

            $color = 'info';
            if ($percent >= 5) {
                $color = 'success';
            }
            if ($percent >= 90) {
                $color = 'warning';
            }
            if ($percent >= 100) {
                $color = 'danger';
            }

            $alerts->push([
                'color' => $color,
                'message' => "<strong>" . ucfirst(__('st-occupation')) . "</strong>: $percent%"
            ]);

            $color = 'info';
            foreach (['current' => 0, 'boarding' => 1, 'activated' => 2] as $type => $event) {
                $data = $occupation->get($type);
                $seatingList = collect(explode(' ', $data));

                if (!$photo && collect(['activated', 'boarding'])->contains($type)) {
                    $data = "";
                    $seatingList = collect([]);
                }

                $total = $data ? $seatingList->count() : 0;
                $totalStr = $total ? " ($total)" : "";

                $alerts->push([
                    'color' => $color,
                    'event' => $event,
                    'total' => $total,
                    'message' => "<strong>" . ucfirst(__("st-$type")) . "$totalStr</strong>: $data"
                ]);

                if ($type == 'activated' && $total) {
                    foreach ($seatingList as $activated) {
                        $seatingCounted[$activated] = [
                            'total' => (intval($seatingCounted[$activated] ?? 0)) + 1,
                            'new' => true
                        ];
                    }

                    $countedSeating = $seatingList;
                }
            }

            $color = 'gray';
            $dataCounted = collect($seatingCounted);
            if ($dataCounted->count()) {
                $countedStr = "";

                $dataCounted = $dataCounted->sortBy(function ($value, $key) {
                    return $key;
                });

                foreach ($dataCounted as $seat => $counted) {
                    $countedTimes = $counted['total'];
                    $colorCounted = $countedTimes > 1 ? "warning" : $color;

                    $newCounted = $counted['new'];
                    $newCountedColor = $newCounted ? "counted" : "";

                    $countedStr .= "<span class='text-$newCountedColor'>$seat</span><small class='text-$colorCounted'>($countedTimes)</small> ";

                    $seatingCounted[$seat]['new'] = false;
                }

                $totalCounted = $dataCounted->count();
                $totalCounted = $totalCounted > 0 ? " ($totalCounted)" : "";

                $alerts->push([
                    'color' => $color,
                    'message' => "<h6>" . strtoupper(__('st-counted')) . "$totalCounted: $countedStr</h6>"
                ]);
            }
        }

        return (object)[
            'alerts' => $alerts->toArray(),
            'countedStr' => $countedSeating->implode(','),
            'types' => $this->processPhotoEventTypes($alerts->toArray())
        ];
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
            $infoDispatchRegister = "$route \n " . __('Round trip') . " $dispatchRegister->trip \n " . __('Turn') . " $dispatchRegister->turn \n " . __('Dispatched') . " $dispatchRegister->departure \n " . __('Driver') . " $dispatchRegister->driver";
        }

        return $infoDispatchRegister;
    }
}
