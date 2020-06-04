<?php

namespace App\Http\Controllers;

use App\Models\Drivers\Driver;
use App\Models\Routes\DispatchRegister;
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
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');

        if ($finalDate < $initialDate) return view('partials.dates.invalidRange');

        $passengerReport = $this->buildPassengerReport($company, $driver, $vehicle, $initialDate, $finalDate);

        if ($request->get('export')) return $this->export($passengerReport);

        return view('reports.passengers.recorders.consolidated.dates.passengersReport', compact('passengerReport'));
    }

    /**
     * Build passenger report from company and date
     *
     * @param Company $company
     * @param Driver|null $driver
     * @param Vehicle|null $vehicle
     * @param $initialDate
     * @param $finalDate
     * @return object
     */
    public function buildPassengerReport(Company $company, Driver $driver = null, Vehicle $vehicle = null, $initialDate, $finalDate)
    {
        $dateRange = PCWTime::dateRange(Carbon::parse($initialDate), Carbon::parse($finalDate));

        $dispatchRegisters = DispatchRegister::whereIn('route_id', $company->routes->pluck('id'))
            ->whereDriver($driver)
            ->whereVehicle($vehicle);

        $dispatchRegisters = $dispatchRegisters
            ->whereBetween('date', [$initialDate, $finalDate])
            ->active()
            ->get()
            ->sortBy('id');

        $passengerBySensorByDates = self::buildReportBySensorByDates($dispatchRegisters);
        $passengerByRecorderByDates = self::buildReportByRecorderByDates($dispatchRegisters);

        $reports = collect([]);
        foreach ($dateRange as $date) {
            $sensor = $passengerBySensorByDates->where('date', $date->toDateString())->first();
            $recorder = $passengerByRecorderByDates->where('date', $date->toDateString())->first();

            $reports->put($date->toDateString(), (object)[
                'date' => $date,
                'totalByRecorder' => $recorder->totalByRecorder ?? 0,
                'totalBySensor' => $sensor->totalBySensor ?? 0,
                'totalBySensorRecorder' => $sensor->totalBySensorRecorder ?? 0,
                'issues' => collect($recorder ? $recorder->issues : []),
                'frame' => '',
                'driverProcessed' => $driver ? $driver->fullName() : ($vehicle ? ($recorder->lastDriverName ?? '') : __('All')),
                'vehicleProcessed' => $vehicle ? $vehicle->number : ($driver ? ($recorder->lastVehicleNumber ?? '') : __('All'))
            ]);
        }

        $passengerReport = (object)[
            'driver' => $driver,
            'driverReport' => $driver ? $driver->id : 'all',
            'vehicle' => $vehicle,
            'vehicleReport' => $vehicle ? $vehicle->id : 'all',
            'initialDate' => $initialDate,
            'finalDate' => $finalDate,
            'companyId' => $company->id,
            'reports' => $reports,
            'totalSensor' => $reports->sum('totalBySensor'),
            'totalRecorder' => $reports->sum('totalByRecorder'),
            'totalSensorRecorder' => $reports->sum('totalBySensorRecorder'),
        ];

        return $passengerReport;
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
        $initialDate = $passengerReport->initialDate;
        $finalDate = $passengerReport->finalDate;

        $dataExcel = array();
        foreach ($passengerReport->reports as $date => $report) {
            $data = collect([
                __('N°') => count($dataExcel) + 1,                          # A CELL
                __('Date') => $date,                                        # B CELL
                __('Driver') => $report->driverProcessed,                                        # B CELL
                __('Vehicle') => $report->vehicleProcessed,                                        # B CELL
                __('Sensor recorder') => $report->totalBySensorRecorder,    # C CELL
                __('Recorder') => $report->totalByRecorder,                 # D CELL
                __('Sensor') => $report->totalBySensor,                     # E CELL
            ]);
            if (Auth::user()->isAdmin()) $data->put(__('Frame'), $report->frame);

            $dataExcel[] = $data->toArray();
        }

        $infoVehicle = $vehicle ? __("Vehicle") . " " . $vehicle->number : "";
        $infoDriver = $driver ? "C$driver->code: $driver->fullName" : "";

        PCWExporterService::excel([
            'fileName' => str_limit(str_limit($infoDriver, 3) . " $infoVehicle" . __('Passengers per dates'), 28),
            'title' => str_limit(($infoDriver ? "$infoDriver\n" : "") . ($infoVehicle ? "$infoVehicle | " : "") . " $initialDate - $finalDate", 100),
            'subTitle' => str_limit(__("Passengers per dates"), 28),
            'data' => $dataExcel,
            'type' => 'passengerReportByRangeTotalFooter'
        ]);
    }

    /**
     * @param $dispatchRegisters
     * @return \Illuminate\Support\Collection
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
                'totalBySensorRecorder' => $report->report->sum('passengersBySensorRecorder')
            ]);
        }

        return $reports->sortBy('date');
    }

    /**
     * @param $dispatchRegisters
     * @return \Illuminate\Support\Collection
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
            ]);
        }

        return $reports->sortBy('date');
    }
}
