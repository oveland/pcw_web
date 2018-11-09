<?php

namespace App\Http\Controllers;

use App\DispatchRegister;
use App\Services\PCWExporterService;
use App\Services\PCWTime;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\Company;

class PassengersReportDateRangeController extends Controller
{
    use CounterByRecorder;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.passengers.recorders.consolidated.dates.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehicle = Vehicle::find(intval($request->get('vehicle-report')));
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');

        if ($finalDate < $initialDate) return view('partials.dates.invalidRange');

        $passengerReport = $this->buildPassengerReport($company, $vehicle, $initialDate, $finalDate);

        return view('reports.passengers.recorders.consolidated.dates.passengersReport', compact('passengerReport'));
    }

    /**
     * Build passenger report from company and date
     *
     * @param $company
     * @param $vehicle
     * @param $initialDate
     * @param $finalDate
     * @return object
     */
    public function buildPassengerReport(Company $company, Vehicle $vehicle = null, $initialDate, $finalDate)
    {
        $dateRange = PCWTime::dateRange(Carbon::parse($initialDate), Carbon::parse($finalDate));

        $dispatchRegisters = DispatchRegister::whereIn('route_id', $company->routes->pluck('id'));
        if ($vehicle) $dispatchRegisters = $dispatchRegisters->where('vehicle_id', $vehicle->id);
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
                'frame' => ''
            ]);
        }

        $passengerReport = (object)[
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
     * @param Request $request
     */
    public function export(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehicle = Vehicle::find(intval($request->get('vehicle-report')));
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');

        $passengerReports = $this->buildPassengerReport($company, $vehicle, $initialDate, $finalDate);

        $dataExcel = array();
        foreach ($passengerReports->reports as $date => $report) {
            $data = collect([
                __('N°') => count($dataExcel) + 1,                          # A CELL
                __('Date') => $date,                                        # B CELL
                __('Sensor recorder') => $report->totalBySensorRecorder,    # C CELL
                __('Recorder') => $report->totalByRecorder,                 # D CELL
                __('Sensor') => $report->totalBySensor,                     # E CELL
            ]);
            if (Auth::user()->isAdmin()) $data->put(__('Frame'), $report->frame);

            $dataExcel[] = $data->toArray();
        }

        $infoVehicle = ($vehicle ? __("#") . $vehicle->number . " " : "");
        PCWExporterService::excel([
            'fileName' => $infoVehicle . __('Consolidated per dates'),
            'title' => __('Passengers report') . "\n $initialDate - $finalDate",
            'subTitle' => str_limit($infoVehicle . __('Consolidated per dates'), 28),
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
            ]);
        }

        return $reports->sortBy('date');
    }
}
