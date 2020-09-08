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
use Auth;
use App\Models\Company\Company;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PassengersReportDateRangeController extends Controller
{
    use CounterByRecorder;

    private $pcwAuthService;

    /**
     * PassengersReportDateRangeController constructor.
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
        $groupedReport = $request->get('grouped-report');

        if ($dateEndReport < $dateReport) return view('partials.dates.invalidRange');

        $passengerReport = $this->buildPassengerReport($company, $routeReport, $vehicle, $driver, $dateReport, $withEndDate, $dateEndReport, $groupedReport);

        if ($request->get('export')) return $this->export($passengerReport);

        return view('reports.passengers.recorders.consolidated.dates.passengersReport', compact('passengerReport'));
    }

    /**
     * @param Company $company
     * @param Route $route
     * @param Vehicle|null $vehicle
     * @param Driver|null $driver
     * @param $dateReport
     * @param $withEndDate
     * @param $dateEndReport
     * @param null $groupedReport
     * @return object
     */
    public function buildPassengerReport(Company $company, Route $route = null, Vehicle $vehicle = null, Driver $driver = null, $dateReport, $withEndDate, $dateEndReport, $groupedReport = null)
    {
        $dateRange = PCWTime::dateRange(Carbon::parse($dateReport), Carbon::parse($dateEndReport));

        $dispatchRegisters = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($company, $dateReport, $dateEndReport, $route->id ?? null, $vehicle->id ?? null)
            ->whereDriver($driver)
            ->active()
            ->with('vehicle')
            ->with('driver')
            ->with('route')
            ->orderBy('id')->get();

        $dispatchRegisters = $dispatchRegisters->sortBy(function (DispatchRegister $dr) {
            return $dr->vehicle->number;
        });

        $reports = collect([]);

        if ($vehicle == null && $groupedReport) {
            $dispatchRegistersByVehicle = $dispatchRegisters->groupBy('vehicle_id');

            foreach ($dispatchRegistersByVehicle as $vehicleId => $dr) {
                $vehicleByDate = Vehicle::find($vehicleId);
                $report = $this->buildReport($dateRange, $route, $vehicleByDate, $driver, $dr);
                $reports = $reports->merge($report);
            }
        } else {
            $reports = $this->buildReport($dateRange, $route, $vehicle, $driver, $dispatchRegisters);
        }

        $allIssues = $reports->pluck('issues')->collapse()->mapWithKeys(function ($m) {
            return [$m->first()->vehicle_id => $m];
        });

        $passengerReport = (object)[
            'route' => $route,
            'routeReport' => $route ? $route->id : 'all',
            'vehicle' => $vehicle,
            'vehicleReport' => $vehicle ? $vehicle->id : 'all',
            'driver' => $driver,
            'driverReport' => $driver ? $driver->id : 'all',
            'dateReport' => $dateReport,
            'dateEndReport' => $dateEndReport,
            'companyId' => $company->id,
            'reports' => $reports,
            'totalSensor' => $reports->sum('totalBySensor'),
            'totalRecorder' => $reports->sum('totalByRecorder'),
            'totalSensorRecorder' => $reports->sum('totalBySensorRecorder'),
            'totalRoundTrips' => $reports->sum('roundTrips'),
            'groupedReport' => $groupedReport,
            'withEndDate' => $withEndDate,
            'issues' => $allIssues,
            'canLiquidate' => ($groupedReport || $vehicle) && !$route
        ];

        return $passengerReport;
    }

    private function buildReport($dateRange, $route, $vehicle, $driver, $dispatchRegisters)
    {
        $reports = collect([]);
        $passengerBySensorByDates = self::buildReportBySensorByDates($dispatchRegisters);
        $passengerByRecorderByDates = self::buildReportByRecorderByDates($dispatchRegisters);

        foreach ($dateRange as $date) {
            $sensor = $passengerBySensorByDates->where('date', $date->toDateString())->first();
            $recorder = $passengerByRecorderByDates->where('date', $date->toDateString())->first();

            $reports->push((object)[
                'date' => $date->toDateString(),
                'totalByRecorder' => $recorder->totalByRecorder ?? 0,
                'totalBySensor' => $sensor->totalBySensor ?? 0,
                'totalBySensorRecorder' => $sensor->totalBySensorRecorder ?? 0,
                'issues' => collect($recorder ? $recorder->issues : []),
                'roundTrips' => $sensor->totalRoundTrips ?? 0,
                'frame' => '',
                'routeProcessed' => $route ? $route->name : __('All'),
                'vehicle' => $vehicle,
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
                __('Sensor recorder') => $report->totalBySensorRecorder,    # G CELL
                __('Recorder') => $report->totalByRecorder,                 # H CELL
                __('Sensor') => $report->totalBySensor,                     # I CELL
            ]);
            if (Auth::user()->isAdmin()) $data->put(__('Frame'), $report->frame);

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
            $date = Carbon::createFromFormat(config('app.date_format'), $date)->format('Y-m-d');
            $report = CounterBySensor::report($dispatchRegistersByDate);

            $reports->put($date, (object)[
                'date' => $date,
                'totalBySensor' => $report->report->sum('passengersBySensor'),
                'totalBySensorRecorder' => $report->report->sum('passengersBySensorRecorder'),
                'totalRoundTrips' => count($dispatchRegistersByDate)
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
            $date = Carbon::createFromFormat(config('app.date_format'), $date)->format('Y-m-d');
            $report = CounterByRecorder::report($dispatchRegistersByDate);

            $reports->put($date, (object)[
                'date' => $date,
                'totalByRecorder' => $report->report->sum('passengers'),
                'issues' => $report->issues,
                'lastVehicleNumber' => $report->lastVehicleNumber,
                'lastDriverName' => $report->lastDriverName,
                'totalRoundTrips' => count($dispatchRegistersByDate)
            ]);
        }

        return $reports->sortBy('date');
    }
}
