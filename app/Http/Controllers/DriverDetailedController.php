<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Http\Controllers\Utils\StrTime;
use App\Services\PCWExporter;
use Auth;
use Illuminate\Http\Request;

class DriverDetailedController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.drivers.detailed.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $dateReport = $request->get('date-report');
        $driverReport = $request->get('driver-report');

        $driverReport = $this->buildDriverReport($company, $dateReport, $driverReport);

        return view('reports.drivers.detailed.show', compact('driverReport'));
    }

    function buildDriverReport($company, $dateReport, $driverReport)
    {
        $report = collect([]);

        $drivers = $company->activeDrivers();
        if ($driverReport) $drivers->whereIn('code', $driverReport);
        $drivers = $drivers->get();

        $dispatchRegistersByDrivers = DispatchRegister::where('date', $dateReport)
            ->completed()
            ->whereIn('driver_code', $drivers->pluck('code'))
            ->orderBy('departure_time')
            ->get()
            ->groupBy('driver_code');

        foreach ($dispatchRegistersByDrivers as $driverCode => $dispatchRegistersByDriver) {
            $deadTimeReport = collect([]);
            $deadTime = '00:00:00';
            $totalDeadTime = '00:00:00';
            $lastArrivalTime = null;
            foreach ($dispatchRegistersByDriver as $dispatchRegister) {
                if ($lastArrivalTime) {
                    $deadTime = StrTime::subStrTime($dispatchRegister['departure_time'], $lastArrivalTime);
                    $totalDeadTime = StrTime::addStrTime($totalDeadTime, $deadTime);
                }
                $deadTimeReport->put($dispatchRegister->id,$deadTime);
                $lastArrivalTime = $dispatchRegister['arrival_time'];
            }

            $report->put($driverCode, (object)[
                'company' => $company->id,
                'dateReport' => $dateReport,
                'driverReport' => $driverReport,
                'dispatchRegisters' => $dispatchRegistersByDriver,
                'totalDeadTime' => $totalDeadTime,
                'deadTimeReport' => $deadTimeReport
            ]);
        }

        return $report->sortBy('totalDeadTime');
    }

}
