<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
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

        $driverReport = 'all';

        $drivers = $company->activeDrivers();
        if( $driverReport != 'all' )$drivers->where('code',$driverReport);
        $drivers = $drivers->get();

        $dispatchRegistersByDrivers = DispatchRegister::where('date',$dateReport)
            ->whereIn('driver_code', $drivers->pluck('code'))
            ->get()
            ->groupBy('driver_code');

        return view('reports.drivers.detailed.show',compact('dispatchRegistersByDrivers'));
    }

    /**
     * Export report to excel format
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $dateReport = $request->get('date-report');

        $passengerReports = $this->buildPassengerReport($company, $dateReport);

        $dataExcel = array();
        foreach ($passengerReports->reports as $report) {
            $vehicle = Vehicle::find($report->vehicle_id);
            $sensor = $report->passengers->sensor;
            $recorder = $report->passengers->recorder;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Recorder') => intval($recorder),                                    # D CELL
                __('Sensor') => intval($sensor),                                        # E CELL
                __('Difference') => abs($sensor - $recorder),                   # F CELL
            ];
        }

        PCWExporter::excel([
            'fileName' => __('Passengers report') . " $dateReport",
            'title' => __('Passengers report') . " $dateReport",
            'subTitle' => __('Consolidated per day'),
            'data' => $dataExcel,
            'type' => 'passengerReportTotalFooter'
        ]);
    }
}