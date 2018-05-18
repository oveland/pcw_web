<?php

namespace App\Http\Controllers;

use App\DispatchRegister;
use App\Models\Passengers\PassengerCounterPerDay;
use App\Models\Passengers\PassengerCounterPerDaySixMonth;
use App\Models\Passengers\RecorderCounterPerDay;
use App\PassengersDispatchRegister;
use App\Route;
use App\Services\PCWExporter;
use App\Services\PCWTime;
use App\Traits\CounterByRecorder;
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
        return view('reports.passengers.consolidated.dates.index', compact('companies'));
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

        return view('reports.passengers.consolidated.dates.passengersReport', compact('passengerReport'));
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
        $recorderReports = $this->buildPassengersReportByRecorder($company, $vehicle, $initialDate, $finalDate);
        $sensorReports = $this->buildPassengersReportBySensor($company, $vehicle, $initialDate, $finalDate);

        $reports = collect([]);
        foreach ($dateRange as $date) {
            $recorderReport = $recorderReports->where('date', $date->toDateString())->first();
            $totalByRecorder = $recorderReport ? $recorderReport->total : 0;

            $sensorReport = $sensorReports->where('date', $date->format(config('app.date_format')));

            $totalBySensor = $sensorReport ? $sensorReport->sum('total') : 0;

            $reports->put($date->toDateString(), (object)[
                'date' => $date,
                'totalByRecorder' => $totalByRecorder,
                'totalBySensor' => $totalBySensor,
                'issues' => collect($recorderReport ? $recorderReport->issues : []),
                'frame' => $sensorReport->first() ? $sensorReport->first()->frame : ''
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
        ];

        return $passengerReport;
    }

    /**
     * @param Company $company
     * @param Vehicle|null $vehicle
     * @param $initialDate
     * @param $finalDate
     * @return \Illuminate\Support\Collection
     */
    public function buildPassengersReportByRecorder(Company $company, Vehicle $vehicle = null, $initialDate, $finalDate)
    {
        $dispatchRegisters = PassengersDispatchRegister::whereIn('route_id', $company->routes->pluck('id'));
        if ($vehicle) $dispatchRegisters = $dispatchRegisters->where('vehicle_id', $vehicle->id);
        $dispatchRegisters = $dispatchRegisters->whereBetween('date', [$initialDate, $finalDate])
            ->active()
            ->get()
            ->sortBy('id');

        return self::report($dispatchRegisters);
    }

    /**
     * @param Company $company
     * @param Vehicle|null $vehicle
     * @param $initialDate
     * @param $finalDate
     * @return \Illuminate\Support\Collection
     */
    public function buildPassengersReportBySensor(Company $company, Vehicle $vehicle = null, $initialDate, $finalDate)
    {
        $sensorReports = PassengerCounterPerDaySixMonth::where('company_id', $company->id);
        if ($vehicle) $sensorReports = $sensorReports->where('vehicle_id', $vehicle->id);
        $sensorReports = $sensorReports->whereBetween('date', [$initialDate, $finalDate])
            ->orderBy('date')
            ->get();
        return $sensorReports;
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
                __('N°') => count($dataExcel) + 1,                  # A CELL
                __('Date') => $date,                                # B CELL
                __('Sensor') => $report->totalBySensor,             # C CELL
                __('Recorder') => $report->totalByRecorder,         # D CELL
                __('Difference') => ''                              # E CELL
            ]);
            if (Auth::user()->isAdmin()) $data->put(__('Frame'), $report->frame);

            $dataExcel[] = $data->toArray();
        }

        $infoVehicle = ($vehicle ? __("#") . $vehicle->number . " " : "");
        PCWExporter::excel([
            'fileName' => $infoVehicle . __('Consolidated per dates'),
            'title' => __('Passengers report') . "\n $initialDate - $finalDate",
            'subTitle' => str_limit($infoVehicle . __('Consolidated per dates'), 28),
            'data' => $dataExcel,
            'type' => 'passengerReportByRangeTotalFooter'
        ]);
    }

    static function report($dispatchRegisters)
    {
        $dispatchRegistersByDates = $dispatchRegisters->sortBy('id')->groupBy('date');

        $reports = array();
        foreach ($dispatchRegistersByDates as $date => $dispatchRegistersByDate) {
            $date = Carbon::createFromFormat(config('app.date_format'), $date)->format('Y-m-d');
            $report = CounterByRecorder::report($dispatchRegistersByDate);

            $reports[$date] = (object)[
                'date' => $date,
                'total' => $report->report->sum('passengers'),
                'issues' => $report->issues,
            ];
        }

        return collect($reports)->sortBy('date');
    }
}
