<?php

namespace App\Http\Controllers;

use App\DispatchRegister;
use App\Models\Passengers\RecorderCounterPerDay;
use App\Services\PCWExporter;
use App\Vehicle;
use Illuminate\Http\Request;
use Auth;
use App\Company;

class PassengersReportDateRangeController extends Controller
{
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
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');

        if ($finalDate < $initialDate) return view('partials.dates.invalidRange');

        $passengerReport = $this->buildPassengerReport($company, $initialDate, $finalDate);

        return view('reports.passengers.consolidated.dates.passengersReport', compact('passengerReport'));
    }

    /**
     * Build passenger report from company and date
     *
     * @param $company
     * @param $initialDate
     * @param $finalDate
     * @return object
     */
    public function buildPassengerReport($company, $initialDate, $finalDate)
    {
        // Query passenger by sensor counter
        /*$ageReport = Carbon::parse($dateReport)->diffInDays(Carbon::now());
        $model = $ageReport <= 5 ? PassengerCounterPerDay::class : PassengerCounterPerDaySixMonth::class;
        $passengersCounterPerDay = $model::whereBetween('date', $initialDate, $finalDate)
            ->where('company_id', $company->id)
            ->get();*/

        // Query passenger by recorder counter
        $recorderCounterPerDates = collect(\DB::select("
            SELECT rcd.date, sum(rcd.passengers) passengers, rcd.vehicle_id, rcd.dispatch_register_id
            FROM recorder_counter_per_days rcd
            WHERE rcd.date BETWEEN '$initialDate' AND '$finalDate' AND rcd.company_id = $company->id
            GROUP BY rcd.date, rcd.vehicle_id, rcd.dispatch_register_id, ORDER BY rcd.date ASC 
        "))->groupBy('date');

        $reports = collect([]);
        foreach ($recorderCounterPerDates as $date => $recorderCounterPerDate) {
            $violations = array();
            $totalPerDate = $recorderCounterPerDate->sum('passengers');
            if ($totalPerDate < 0 || $totalPerDate > config('road.max_recorder_counter_per_day_threshold')) {
                foreach ($recorderCounterPerDate as $recorderPassengersByVehicle) {
                    $totalPerVehicle = $recorderPassengersByVehicle->passengers;
                    if ($totalPerVehicle < 0 || $totalPerVehicle > config('road.max_recorder_counter_per_vehicle_threshold')) {
                        $violations[] = DispatchRegister::find($recorderPassengersByVehicle->dispatch_register_id);
                    }
                }
            }

            $reports[$date] = (object)[
                'total' => $totalPerDate,
                'violations' => collect($violations),
            ];
        }

        $passengerReport = (object)[
            'initialDate' => $initialDate,
            'finalDate' => $finalDate,
            'companyId' => $company->id,
            'reports' => $reports,
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
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');

        $passengerReports = $this->buildPassengerReport($company, $initialDate, $finalDate);

        $dataExcel = array();
        foreach ($passengerReports->reports as $date => $report) {
            $recorder = $report->total;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Date') => $date,                               # B CELL
                __('Recorder') => $recorder,                                    # F CELL
            ];
        }

        PCWExporter::excel([
            'fileName' => __('Consolidated per date range'),
            'title' => __('Passengers report'."\n $initialDate - $finalDate"),
            'subTitle' => __('Consolidated per date range'),
            'data' => $dataExcel,
            'type' => 'passengerReportByRangeTotalFooter'
        ]);
    }
}
