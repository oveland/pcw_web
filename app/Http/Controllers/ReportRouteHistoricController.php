<?php

namespace App\Http\Controllers;

use App\Models\Apps\Rocket\VehicleCamera;
use App\Models\Company\Company;
use App\Models\Routes\Dispatch;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\LocationToday;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleStatus;
use App\Services\Auth\PCWAuthService;
use App\Services\Exports\PCWExporterService;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Http\Controllers\Utils\StrTime;

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

        $companyReport = $request->get('c');
        $dateReport = $request->get('d');
        $dateEndReport = $request->get('de');

        $dateReport = $dateReport ? Carbon::createFromFormat('Y-m-d_H:i:s', $dateReport) : null;
        $dateEndReport = $dateEndReport ? Carbon::createFromFormat('Y-m-d_H:i:s', $dateEndReport) : null;
        $withRange = $dateReport && $dateEndReport && $dateReport->toDateString() != $dateEndReport->toDateString();

        $initialTime = $dateReport ? $this->parseTimeQuery($dateReport->toTimeString()) : null;
        $finalTime = $dateEndReport ? $this->parseTimeQuery($dateEndReport->toTimeString()) : null;

        $vehicleReport = Vehicle::where('number', $request->get('n'))
            ->where('company_id', $companyReport ?? 14)->first();
        $vehicleReport = $vehicleReport ? $vehicleReport->id : null;
        $vehicleReport = $vehicleReport ?: $request->get('v');

        $speed = $request->get('s');
        $hideMenu = session('hide-menu') || $request->get('hide-menu');


        return view('reports.route.historic.index', compact(['companies', 'routes', 'vehicles', 'dateReport', 'dateEndReport', 'initialTime', 'finalTime', 'withRange', 'vehicleReport', 'companyReport', 'speed', 'hideMenu']));
    }

    function parseTimeQuery($time)
    {
        if (!$time) return $time;

        $timeFragments = collect(explode(':', $time));
        if ($timeFragments->count() >= 2) {
            return intval(StrTime::toSeg($timeFragments->get(0) . ":" . $timeFragments->get(1)) / 5) ?: 1;
        }

        return intval($time) ?: 1;
    }

    function getDateRangeQuery(Request $request)
    {
        $byRange = $request->get('with-end-date');
        $startDate = $request->get('date-report');
        $endDate = $byRange ? $request->get('date-end-report') : $startDate;
        list($startTime, $endTime) = explode(';', $request->get('time-range-report'));

        if ($byRange) {
            list($startDate, $startTime) = explode(' ', $startDate);
            list($endDate, $endTime) = explode(' ', $endDate);
        }

        $start = Carbon::createFromFormat('Y-m-d H:i', "$startDate $startTime");
        $end = Carbon::createFromFormat('Y-m-d H:i', "$endDate $endTime");

        $message = '';
        $invalid = $end->diffInDays($start) >= 2;
        if ($invalid) {
            $message = 'El rango de fechas a consultar debe ser máximo de 2 días';
            $check = false;
        }

        return (object)[
            'check' => (object)[
                'success' => !$invalid,
                'message' => $message
            ],
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
        ];
    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function show(Request $request)
    {
        $vehicleReport = $request->get('vehicle-report');
        $forExport = $request->get('export');

        $dateTimeQuery = $this->getDateRangeQuery($request);
        if (!$dateTimeQuery->check->success) return response()->json($dateTimeQuery->check);

        $report = $this->buildHistoric($vehicleReport, $dateTimeQuery->start, $dateTimeQuery->end);

        if ($forExport) $this->export($report);
        $report->exportLink = $request->getRequestUri() . "&export=true";

        return response()->json($report);
    }

    public function buildHistoric($vehicleReport, $dateTimeStart, $dateTimeEnd)
    {
        $vehicle = Vehicle::find($vehicleReport);

        $locations = Location::forDate($dateTimeStart, $dateTimeEnd)->whereBetween('date', [$dateTimeStart, $dateTimeEnd])
            ->where('vehicle_id', $vehicleReport)
            ->where('vehicle_status_id', '<>', 2) // Excludes status disengaged
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

        $vehicleCameras = $vehicle->cameras;
        $photos = [];
        $prevEvents = [
            'alerts' => [],
            'countedStr' => '',
            'types' => [],
        ];
        $photosCounter = collect([]);

        foreach ($locations as $index => $location) {
            $dispatchRegister = $location->dispatchRegister;
            $inRoute = $dispatchRegister && $dispatchRegister->isActive();

//            $period = '';
//            $averagePeriod = '';
//            if (Auth::user()->isAdmin()) {
//                $period = $location->date->diffInSeconds($lastLocation->date);
//                $averagePeriod = "--";
//            }

            $passenger = $location->passenger;

            if ($passenger) {
//                $dispatchRegister = $passenger->dispatchRegister;

                if ($dispatchRegister) {
                    $newTurn = !$prevDr || $dispatchRegister->id != $prevDr->id;
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

                    //$countedAscents = $passenger->ascents_in_round_trip ?? false;
                    //if ($passenger->counted) $countedAscents = false;
                    //$countedDescents = $passenger->descents_in_round_trip && Auth::user()->isAdmin() ?? false;
                    //$countedDescents = false;

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

                if ($dispatchRegister && $dispatchRegister->isActive()) {
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

            $locationPhotos = $location->photos->count() ? collect($location->photos->toArray())->pluck('id', 'side') : collect([]);
            $photos = $vehicleCameras->map(function (VehicleCamera $vc) use ($locationPhotos, $photosCounter) {
                $id = $locationPhotos->get($vc->camera);
                $counter = ($photosCounter->get($vc->camera) ?? 0) + ($id ? 1 : 0);
                $photosCounter->put($vc->camera, $counter);
                return [
                    'cm' => $vc->camera,
                    'id' => $id,
                    'cn' => $counter
                ];
            });


            $photoEvents = $this->processPhotoEvents($location->photo, $photoTags, $seatingCounted);
            if (!count($photoEvents->alerts)) $photoEvents = $prevEvents;

            $dataLocations->push((object)[
                'id' => $location->id,
//                'inRoute' => $inRoute,
                'time' => $location->date->format('H:i:s'),
//                'period' => $period,
//                'averagePeriod' => $averagePeriod,
                'date' => $location->date->format('Y-m-d'),
                'currentMileage' => number_format(intval($location->current_mileage) / 1000, 2, '.', ''),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
//                'address' => "",
//                'odometer' => $location->odometer,
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
                    "events" => $photoEvents
                ],
                'photos' => $photos,
            ]);

            $prevTotalPassengers = $totalPassengers;
            $lastLocation = $location;

            $prevEvents = $photoEvents;
        }

        $totalLocations = $dataLocations->count();

        return (object)[
            'success' => true,
            'dateTimeStart' => $dateTimeStart,
            'dateTimeEnd' => $dateTimeEnd,
            'vehicle' => $vehicle->getAPIFields(null, true),
            'vehicleCameras' => $vehicle->cameras->pluck('camera'),
            'historic' => $dataLocations,
            'total' => $totalLocations,
            'from' => $totalLocations ? $dataLocations->first()->time : '--:--',
            'to' => $totalLocations ? $dataLocations->last()->time : '--:--',
            'config' => [
                'events' => [
                    'panic' => $vehicle->company->id === Company::COODETRANS
                ],
                'show' => [
                    'passengers' => Auth::user()->company_id != Company::EXPRESO_PALMIRA,
                    'geofenceDispatches' => Auth::user()->isAdmin(),
                ],
                'dispatches' => $vehicle->company->dispatches()->where('active', true)->get()->map(function (Dispatch $d) {
                    return (object)[
                        'latitude' => $d->latitude,
                        'longitude' => $d->longitude,
                        'radius' => $d->radio_geofence,
                        'color' => '#ff8700'
                    ];
                })
            ]
        ];
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
                            'total' => (intval(isset($seatingCounted[$activated]) ? $seatingCounted[$activated]['total'] : 0)) + 1,
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

        if (!Auth::user()->isSuperAdmin() && !Auth::user()->company_id == 39) {
            $alerts = collect([]);
        }

        return (object)[
            'alerts' => $alerts->toArray(),
            'countedStr' => $countedSeating->implode(','),
            'types' => $this->processPhotoEventTypes($alerts->toArray())
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
            'fileName' => "H " . $report->vehicle->number . " $report->dateTimeStart - $report->dateTimeEnd",
            'title' => __('Historic') . " #" . $report->vehicle->number,
            'subTitle' => "$report->dateTimeStart - $report->dateTimeEnd",
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
