<?php

namespace App\Http\Controllers;

use App\Models\Drivers\Driver;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use App\Services\PCWTime;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PassengersRecordersReportController extends Controller
{
    const PROGRAMMED_ROUND_TRIPS = 5; // TODO: Parameterize $programmedRoundTrips constant value
    const LIMIT_DATE_RANGE = 31;

    use CounterByRecorder;

    private $pcwAuthService;

    /**
     * PassengersRecordersReportController constructor.
     * @param PCWAuthService $pcwAuthService
     */
    public function __construct(PCWAuthService $pcwAuthService)
    {
        $this->pcwAuthService = $pcwAuthService;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $accessProperties = $this->pcwAuthService->getAccessProperties();
        $companies = $accessProperties->companies;
        $drivers = $accessProperties->company->drivers;

        return view('reports.passengers.recorders.consolidated.dates.index', compact(['companies', 'drivers']));
    }

    /**
     * @param Request $request
     * @return Factory|Application|View|void
     */
    public function show(Request $request)
    {
        $company = $this->pcwAuthService->getCompanyFromRequest($request);
        $vehicle = Vehicle::find(intval($request->get('vehicle-report')));
        $driver = Driver::find(intval($request->get('driver-report')));
        $routeReport = Route::find(intval($request->get('route-report')));;
        $dateReport = $request->get('date-report');
        $withEndDate = $request->get('with-end-date');
        $dateEndReport = $withEndDate ? $request->get('date-end-report') : $dateReport;
        $groupByVehicle = $request->get('group-by-vehicle');
        $groupByRoute = $request->get('group-by-route');
        $groupByDate = $request->get('group-by-date');

        if ($dateEndReport < $dateReport) return view('partials.dates.invalidRange');
        if (Carbon::parse($dateReport)->diffInDays(Carbon::parse($dateEndReport)) > self::LIMIT_DATE_RANGE) return view('partials.dates.rangeTooHigh', ['limit' => self::LIMIT_DATE_RANGE]);

        $passengerReport = $this->buildPassengerReport($company, $routeReport, $vehicle, $driver, $dateReport, $withEndDate, $dateEndReport, $groupByVehicle, $groupByRoute, $groupByDate);

        if ($request->get('export')) return $this->export($passengerReport);

        return view('reports.passengers.recorders.consolidated.dates.passengersReport', compact('passengerReport'));
    }

    /**
     * @param Company $company
     * @param Route|null $route
     * @param Vehicle|null $vehicle
     * @param Driver|null $driver
     * @param $dateReport
     * @param $withEndDate
     * @param $dateEndReport
     * @param null $groupByVehicle
     * @param null $groupByRoute
     * @param null $groupByDate
     * @return object
     */
    public function buildPassengerReport(Company $company, Route $route = null, Vehicle $vehicle = null, Driver $driver = null, $dateReport, $withEndDate, $dateEndReport, $groupByVehicle = null, $groupByRoute = null, $groupByDate = null)
    {
        $dateRange = PCWTime::dateRange(Carbon::parse($dateReport), Carbon::parse($dateEndReport));

        $allDispatchRegisters = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($company, $dateReport, $dateEndReport, $route->id ?? null, $vehicle->id ?? null)
            ->whereDriver($driver)
            ->active()
            ->with('vehicle')
            ->with('driver')
            ->with('route')
            ->orderBy('id')->get();

        $allDispatchRegisters = $allDispatchRegisters->sortBy(function (DispatchRegister $dr) {
            return $dr->vehicle->number;
        });

        $reports = collect([]);

        $dateRange = collect($dateRange);
        foreach ($dateRange as $date) {
            $dispatchRegisters = $allDispatchRegisters->where('date', $date->toDateString());

//            $firstRoute = $dispatchRegisters->count() ? $dispatchRegisters->sortBy('departure_time')->first()->route : null;
//            $programmedRoundTrips = self::PROGRAMMED_ROUND_TRIPS;


            $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id');

            $reportsByVehicles = collect([]);
            foreach ($dispatchRegistersByVehicles as $vehicleId => $dr) {

                $vehicleByDate = Vehicle::find($vehicleId);
                $dr = collect($dr);

                $firstRoute = $dr->sortBy('departure_time')->first()->route;

                $dispatchRegistersByRoutes = $dr->sortBy(function ($d) {
                    return $d->route->name;
                })->groupBy('route_id');

                $totalRoutes = $dispatchRegistersByRoutes->count();
                $index = 1;
                $roundTripsCounter = 0;

                $reportsByRoutes = collect([]);
                foreach ($dispatchRegistersByRoutes as $routeId => $drr) {

                    $routeByDateAndVehicle = Route::find($routeId);

                    $countRoundTrips = collect($drr)->count();

                    $programmedRoundTrips = $index != $totalRoutes ? $countRoundTrips : (self::PROGRAMMED_ROUND_TRIPS - $roundTripsCounter);

                    $roundTripsCounter += $countRoundTrips;
                    $index++;

                    $reportsByRoute = $this->buildReport($date, $routeByDateAndVehicle, $vehicleByDate, $driver, $drr, $programmedRoundTrips, $firstRoute);
                    $reportsByRoutes = $reportsByRoutes->merge($reportsByRoute);
                }

                if (!$groupByRoute && $reportsByRoutes->count()) {
                    $ungrouped = collect([]);
                    foreach ($reportsByRoutes->groupBy('vehicleId') as $vehicleFilterId => $r) {
                        $mileage = $r->sum('mileage');
                        $totalByRecorder = $r->sum('totalByRecorder');
                        $IPK = $mileage > 0 ? $totalByRecorder / $mileage : 0;

                        $programmedMileage = $r->sum('programmedMileage');
                        $differenceMileage = $mileage - $programmedMileage;

                        $ungrouped->push((object)[
                            'date' => $date->toDateString(),
                            'totalByRecorder' => $totalByRecorder,
                            'totalBySensor' => $r->sum('totalBySensor'),
                            'totalAllBySensor' => $r->sum('totalAllBySensor'),
                            'totalBySensorRecorder' => $r->sum('totalBySensorRecorder'),
                            'issues' => $r->pluck('issues')->collapse(),
                            'roundTrips' => $r->sum('roundTrips'),
                            'mileage' => $mileage,
                            'programmedMileage' => $programmedMileage,
                            'differenceMileage' => $differenceMileage,
                            'IPK' => $IPK,
                            'frame' => '',
                            'routeId' => $route->id ?? 0,
                            'route' => $route,
                            'vehicleId' => $vehicleFilterId,
                            'vehicle' => $vehicleByDate,
                            'driver' => $driver,
                            'routeProcessed' => $route ? $route->name : __('All'),
                            'vehicleProcessed' => $vehicleByDate ? $vehicleByDate->number : __('All'),
                            'driverProcessed' => $driver ? $driver->code . ' | ' . $driver->fullName() : __('All'),
                        ]);
                    }
                    $reportsByRoutes = $ungrouped;
                }

                $reportsByVehicles = $reportsByVehicles->merge($reportsByRoutes);
            }

            if (!$groupByVehicle && $reportsByVehicles->count()) {
                $ungrouped = collect([]);

                foreach ($reportsByVehicles->groupBy('routeId') as $routeFilterId => $r) {
                    $routeFilter = Route::find($routeFilterId);

                    $mileage = $r->sum('mileage');
                    $totalByRecorder = $r->sum('totalByRecorder');
                    $IPK = $mileage > 0 ? $totalByRecorder / $mileage : 0;

                    $programmedMileage = $r->sum('programmedMileage');
                    $differenceMileage = $mileage - $programmedMileage;

                    $ungrouped->push((object)[
                        'date' => $date->toDateString(),
                        'totalByRecorder' => $totalByRecorder,
                        'totalBySensor' => $r->sum('totalBySensor'),
                        'totalAllBySensor' => $r->sum('totalAllBySensor'),
                        'totalBySensorRecorder' => $r->sum('totalBySensorRecorder'),
                        'issues' => $r->pluck('issues')->collapse(),
                        'roundTrips' => $r->sum('roundTrips'),
                        'mileage' => $mileage,
                        'programmedMileage' => $programmedMileage,
                        'differenceMileage' => $differenceMileage,
                        'IPK' => $IPK,
                        'frame' => '',
                        'routeId' => $routeFilterId,
                        'route' => $routeFilter,
                        'vehicleId' => $vehicle->id ?? 0,
                        'vehicle' => $vehicle,
                        'driver' => $driver,
                        'routeProcessed' => $routeFilter ? $routeFilter->name : __('All'),
                        'vehicleProcessed' => $vehicle ? $vehicle->number : __('All'),
                        'driverProcessed' => $driver ? $driver->code . ' | ' . $driver->fullName() : __('All'),
                    ]);
                }
                $reportsByVehicles = $ungrouped;
            }

            if ($reportsByVehicles->isEmpty()) {
                $reportsByVehicles->push((object)[
                    'date' => $date->toDateString(),
                    'totalByRecorder' => 0,
                    'totalBySensor' => 0,
                    'totalAllBySensor' => 0,
                    'totalBySensorRecorder' => 0,
                    'issues' => collect([]),
                    'roundTrips' => 0,
                    'mileage' => 0,
                    'programmedMileage' => 0,
                    'differenceMileage' => 0,
                    'IPK' => 0,
                    'frame' => '',
                    'routeId' => $route->id ?? 0,
                    'route' => $route,
                    'vehicleId' => $vehicle->id ?? 0,
                    'vehicle' => $vehicle,
                    'driver' => $driver,
                    'routeProcessed' => __('No data'),
                    'vehicleProcessed' => __('No data'),
                    'driverProcessed' => __('No data'),
                ]);
            }

            $reports = $reports->merge($reportsByVehicles);
        }


        if (!$groupByDate && $reports->count()) {
            $dateRangeStr = $dateRange->first()->toDateString() . " a " . $dateRange->last()->toDateString();

            $ungrouped = collect([]);
            foreach ($reports->groupBy('vehicleId') as $vehicleFilterId => $r) {
                $vehicleFilter = Vehicle::find($vehicleFilterId);

                foreach ($r->groupBy('routeId') as $routeFilterId => $rr) {
                    $routeFilter = Route::find($routeFilterId);

                    $mileage = $rr->sum('mileage');
                    $totalByRecorder = $rr->sum('totalByRecorder');
                    $IPK = $mileage > 0 ? $totalByRecorder / $mileage : 0;

                    $programmedMileage = $rr->sum('programmedMileage');
                    $differenceMileage = $mileage - $programmedMileage;

                    $ungrouped->push((object)[
                        'date' => $dateRangeStr,
                        'totalByRecorder' => $totalByRecorder,
                        'totalBySensor' => $rr->sum('totalBySensor'),
                        'totalAllBySensor' => $rr->sum('totalAllBySensor'),
                        'totalBySensorRecorder' => $rr->sum('totalBySensorRecorder'),
                        'issues' => $rr->pluck('issues')->collapse(),
                        'roundTrips' => $rr->sum('roundTrips'),
                        'mileage' => $mileage,
                        'programmedMileage' => $programmedMileage,
                        'differenceMileage' => $differenceMileage,
                        'IPK' => $IPK,
                        'frame' => '',
                        'routeId' => $routeFilterId,
                        'route' => $routeFilter,
                        'vehicleId' => $vehicleFilterId,
                        'vehicle' => $vehicleFilter,
                        'driver' => $driver,
                        'routeProcessed' => $routeFilter ? $routeFilter->name : __('All'),
                        'vehicleProcessed' => $vehicleFilter ? $vehicleFilter->number : __('All'),
                        'driverProcessed' => $driver ? $driver->code . ' | ' . $driver->fullName() : __('All'),
                    ]);
                }
            }

            $reports = $ungrouped;
        }

        $allIssues = $reports->pluck('issues')->collapse()->mapWithKeys(function ($m) {
            return [$m->first()->vehicle_id => $m];
        });

        $IPK = $reports->sum('mileage') > 0 ? $reports->sum('totalByRecorder') / $reports->sum('mileage') : 0;

        return (object)[
            'route' => $route,
            'routeReport' => $route ? $route->id : 'all',
            'vehicle' => $vehicle,
            'vehicleReport' => $vehicle ? $vehicle->id : 'all',
            'driver' => $driver,
            'driverReport' => $driver ? $driver->id : 'all',
            'dateReport' => $dateReport,
            'dateEndReport' => $dateEndReport,
            'company' => $company,
            'companyId' => $company->id,
            'reports' => $reports,
            'totalSensor' => $reports->sum('totalBySensor'),
            'totalAllBySensor' => $reports->sum('totalAllBySensor'),
            'totalRecorder' => $reports->sum('totalByRecorder'),
            'totalSensorRecorder' => $reports->sum('totalBySensorRecorder'),
            'totalRoundTrips' => $reports->sum('roundTrips'),
            'totalMileage' => $reports->sum('mileage'),
            'totalProgrammedMileage' => $reports->sum('programmedMileage'),
            'totalDifferenceMileage' => $reports->sum('differenceMileage'),
            'IPK' => $IPK,
            'groupByVehicle' => $groupByVehicle,
            'groupByRoute' => $groupByRoute,
            'groupByDate' => $groupByDate,
            'withEndDate' => $withEndDate,
            'issues' => $allIssues,
            'canLiquidate' => ($groupByVehicle || $vehicle) && !$route && !$groupByRoute && $groupByDate
        ];
    }

    /**
     * @param Collection | DispatchRegister | DispatchRegister[] $dispatchRegisters
     * @param $programmedRoundTrips
     * @param Route|null $firstRoute
     * @return int
     */
    private function getProgrammedMileage($dispatchRegisters, $programmedRoundTrips, Route $firstRoute = null)
    {
        $programmedMileage = 0;
        if ($dispatchRegisters->count()) {
            $firstRoute = $firstRoute ? $firstRoute : $dispatchRegisters->sortBy('departure_time')->first()->route;
            $programmedMileage = $programmedRoundTrips * $firstRoute->distance_in_km;
        }

        return $programmedMileage;
    }

    /**
     * @param Carbon | array $date
     * @param $route
     * @param $vehicle
     * @param $driver
     * @param $dispatchRegisters
     * @param int $programmedRoundTrips
     * @param null $firstRoute
     * @return Collection
     */
    private function buildReport($date, $route, $vehicle, $driver, $dispatchRegisters, $programmedRoundTrips = self::PROGRAMMED_ROUND_TRIPS, $firstRoute = null, $includeEmptyValues = false)
    {
        $reports = collect([]);
        $sensor = self::buildReportBySensorByDates($dispatchRegisters)->where('date', $date->toDateString())->first();
        $recorder = self::buildReportByRecorderByDates($dispatchRegisters)->where('date', $date->toDateString())->first();

        $programmedMileage = $this->getProgrammedMileage($dispatchRegisters, $programmedRoundTrips, $firstRoute);

        if ($dispatchRegisters->count() || $includeEmptyValues) {
            $mileage = $recorder->mileage ?? 0;
            $totalRoundTrips = $recorder->totalRoundTrips ?? 0;
            $programmedMileage = $totalRoundTrips > 0 ? $programmedMileage : 0;
            $differenceMileage = $mileage - $programmedMileage;
            $IPK = $mileage > 0 ? $recorder->totalByRecorder / $mileage : 0;

            $reports->push((object)[
                'date' => $date->toDateString(),
                'totalByRecorder' => $recorder->totalByRecorder ?? 0,
                'totalBySensor' => $sensor->totalBySensor ?? 0,
                'totalAllBySensor' => $sensor->totalAllBySensor ?? 0,
                'totalBySensorRecorder' => $sensor->totalBySensorRecorder ?? 0,
                'issues' => collect($recorder ? $recorder->issues : []),
                'roundTrips' => $totalRoundTrips,
                'mileage' => $mileage,
                'programmedMileage' => $programmedMileage,
                'differenceMileage' => $differenceMileage,
                'IPK' => $IPK,
                'frame' => '',
                'routeId' => $route->id ?? null,
                'route' => $route,
                'vehicleId' => $vehicle->id ?? null,
                'vehicle' => $vehicle,
                'driver' => $driver,
                'routeProcessed' => $route ? $route->name : __('All'),
                'vehicleProcessed' => $vehicle ? $vehicle->number : ($driver ? ($recorder->lastVehicleNumber ?? '') : __('All')),
                'driverProcessed' => $driver ? $driver->code . ' | ' . $driver->fullName() : ($vehicle ? ($recorder->lastDriverName ?? '') : __('All')),
            ]);
        }

        return $reports;
    }

    /**
     * Export report to excel format
     *
     * @param $passengerReport
     */
    public function export($passengerReport)
    {
        $vehicle = $passengerReport->vehicle;
        $driver = $passengerReport->driver;
        $dateReport = $passengerReport->dateReport;
        $dateEndReport = $passengerReport->dateEndReport;

        $dataExcel = array();
        foreach ($passengerReport->reports as $date => $report) {
            $data = collect([
                __('N°') => count($dataExcel) + 1,                          # A CELL
                __('Date') => $report->date,                                # B CELL
                __('Route') => $report->routeProcessed,                     # C CELL
                __('Vehicle') => $report->vehicleProcessed,                 # D CELL
                __('Driver') => $report->driverProcessed,                   # E CELL
                __('Total round trips') => $report->roundTrips,             # F CELL
                __('Mileage round trips') => $report->mileage,              # G CELL
                __('Mileage programmed') => $report->programmedMileage,     # H CELL
                __('Difference mileage') => $report->differenceMileage,     # I CELL
                __('Recorder') => $report->totalByRecorder,                 # J CELL
                __('IPK') => $report->IPK,                                  # K CELL
            ]);
//            if (Auth::user()->isAdmin()) $data->put(__('Frame'), $report->frame);
            $dataExcel[] = $data->toArray();
        }

        $infoVehicle = $vehicle ? __("Vehicle") . " " . $vehicle->number : "";
        $infoDriver = $driver ? "C$driver->code: $driver->fullName" : "";

        PCWExporterService::excel([
            'fileName' => str_limit(str_limit($infoDriver, 3) . " $infoVehicle" . __('Passengers per dates'), 28),
            'title' => str_limit(($infoDriver ? "$infoDriver\n" : "") . ($infoVehicle ? "$infoVehicle | " : "") . " $dateReport - $dateEndReport", 100),
            'subTitle' => str_limit(__("Passengers per dates"), 28),
            'data' => $dataExcel,
            'type' => 'passengerReportByRangeTotalFooter'
        ]);
    }

    /**
     * @param $dispatchRegisters
     * @return Collection
     */
    static function buildReportBySensorByDates($dispatchRegisters)
    {
        $dispatchRegistersByDates = $dispatchRegisters->sortBy('id')->groupBy('date');

        $reports = collect([]);
        foreach ($dispatchRegistersByDates as $date => $dispatchRegistersByDate) {
            $report = CounterBySensor::report($dispatchRegistersByDate);

            $reports->put($date, (object)[
                'date' => $date,
                'totalBySensor' => $report->report->sum('passengersBySensor'),
                'totalAllBySensor' => $report->report->sum('passengersAllBySensor'),
                'totalBySensorRecorder' => $report->report->sum('passengersBySensorRecorder'),
                'totalRoundTrips' => count($dispatchRegistersByDate),
                'mileage' => $dispatchRegistersByDate->sum('mileage'),
            ]);
        }

        return $reports->sortBy('date');
    }

    /**
     * @param $dispatchRegisters
     * @return Collection
     */
    static function buildReportByRecorderByDates($dispatchRegisters)
    {
        $dispatchRegistersByDates = $dispatchRegisters->sortBy('id')->groupBy('date');

        $reports = collect([]);
        foreach ($dispatchRegistersByDates as $date => $dispatchRegistersByDate) {
            $report = CounterByRecorder::report($dispatchRegistersByDate);

            $reports->put($date, (object)[
                'date' => $date,
                'totalByRecorder' => $report->report->sum('passengers'),
                'issues' => $report->issues,
                'lastVehicleNumber' => $report->lastVehicleNumber,
                'lastDriverName' => $report->lastDriverName,
                'totalRoundTrips' => count($dispatchRegistersByDate),
                'mileage' => $dispatchRegistersByDate->sum('mileage'),
            ]);
        }

        return $reports->sortBy('date');
    }
}
