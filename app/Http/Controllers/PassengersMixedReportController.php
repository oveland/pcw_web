<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\Http\Controllers\Utils\StrTime;
use App\Route;
use App\Services\PCWExporterService;
use App\Traits\CounterByRecorder;
use App\Vehicle;
use Auth;
use Excel;
use Illuminate\Http\Request;

class PassengersMixedReportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::where('active', '=', true)->orderBy('short_name', 'asc')->get();
        }
        return view('reports.passengers.mixed.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $companyReport = $request->get('company-report');
        $routeReport = $request->get('route-report');
        $dateReport = $request->get('date-report');
        $vehiclesReport = Vehicle::where('plate','VCH-351')
            ->orWhere('plate','VCK-542')
            ->orWhere('plate','VCD-672')
            ->orWhere('plate','VCK-531')
            ->get();
        $typeReport = 'group-vehicles';

        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;
        $route = $routeReport == "all" ? $routeReport : Route::find($routeReport);
        if ($routeReport != "all" && (!$route || !$route->belongsToCompany($company))) abort(404);

        $dispatchRegisters = DispatchRegister::where('date', '=', $dateReport);

        if ($routeReport != "all") $dispatchRegisters = $dispatchRegisters->where('route_id', '=', $route->id);
        else $dispatchRegisters = $dispatchRegisters->whereIn('route_id', $company->routes->pluck('id'));

        $dispatchRegisters->whereIn('vehicle_id', $vehiclesReport->pluck('id'));



        $dispatchRegisters = $dispatchRegisters
            ->active()
            ->orderBy('departure_time')
            ->get();

        $dispatchRegistersByVehicles = $dispatchRegisters->groupBy('vehicle_id')
            ->sortBy(function ($reports, $vehicleID) {
                return $reports->first()->vehicle->number;
            });

        $reportsByVehicle = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $reportsByVehicle->put($vehicleId, CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegistersByVehicle));
        }

        if ($request->get('export')) $this->exportByVehicle($dispatchRegistersByVehicles, $dateReport);

        return view('reports.passengers.mixed.reportByVehicle', compact(['dispatchRegistersByVehicles', 'reportsByVehicle', 'company', 'route', 'dateReport', 'routeReport', 'typeReport']));
    }

    /**
     * Export excel by Vehicle option
     *
     * @param $vehiclesDispatchRegisters
     * @param $dateReport
     * @internal param $roundTripDispatchRegisters
     */
    public function exportByVehicle($vehiclesDispatchRegisters, $dateReport)
    {
        Excel::create(__('Dispatch report') . " B " . " $dateReport", function ($excel) use ($vehiclesDispatchRegisters, $dateReport) {
            foreach ($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters) {
                $vehicle = Vehicle::find($vehicleId);
                $vehicleCounter = CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegisters);
                $dataExcel = array();
                $lastArrivalTime = null;

                $totalDeadTime = '00:00:00';

                foreach ($dispatchRegisters as $dispatchRegister) {
                    $historyCounter = $vehicleCounter->report->history[$dispatchRegister->id];
                    $route = $dispatchRegister->route;
                    $driver = $dispatchRegister->driver;

                    $endRecorder = $historyCounter->endRecorder;
                    $startRecorder = $historyCounter->startRecorder;
                    $totalRoundTrip = $historyCounter->passengersByRoundTrip;
                    $totalPassengersByRoute = $historyCounter->totalPassengersByRoute;

                    $deadTime = $lastArrivalTime ? StrTime::subStrTime($dispatchRegister->departure_time, $lastArrivalTime) : '';

                    $dataExcel[] = [
                        __('Route') => $route->name,                                                                    # A CELL
                        __('Round Trip') => $dispatchRegister->round_trip,                                              # B CELL
                        __('Turn') => $dispatchRegister->turn,                                                          # C CELL
                        __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),                        # D CELL
                        __('Departure time') => StrTime::toString($dispatchRegister->departure_time),                   # E CELL
                        __('Arrival Time Scheduled') => StrTime::toString($dispatchRegister->arrival_time_scheduled),   # F CELL
                        __('Arrival Time') => StrTime::toString($dispatchRegister->arrival_time),                       # G CELL
                        __('Arrival Time Difference') => StrTime::toString($dispatchRegister->arrival_time_difference), # H CELL
                        __('Route Time') =>
                            $dispatchRegister->complete() ?
                                StrTime::subStrTime($dispatchRegister->arrival_time, $dispatchRegister->departure_time) :
                                '',                                                                                         # I CELL
                        __('Status') => $dispatchRegister->status,                                                     # J CELL
                        __('Start Rec.') => intval($startRecorder),                                                    # K CELL
                        __('End Rec.') => intval($endRecorder),                                                        # L CELL
                        __('Pass.') . " " . __('Round Trip') => intval($totalRoundTrip),                          # M CELL
                        __('Pass.') . " " . __('Day') => intval($totalPassengersByRoute),                         # N CELL
                        __('Vehicles without route') => intval($dispatchRegister->available_vehicles),                 # O CELL
                        __('Dead time') => $deadTime,                 # P CELL
                    ];

                    $totalDeadTime = $deadTime ? StrTime::addStrTime($totalDeadTime, $deadTime) : $totalDeadTime;

                    $lastArrivalTime = $dispatchRegister->arrival_time;
                }

                $dataExport = (object)[
                    'fileName' => __('Dispatch report') . " V $dateReport",
                    'title' => __('Dispatch report') . " | $dateReport",
                    'subTitle' => "$vehicle->number | $vehicle->plate" . ". " . __('Total dead time') . ": $totalDeadTime",
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'routeReportByVehicle'
                ];
                /* SHEETS */
                $excel = PCWExporterService::createHeaders($excel, $dataExport);
                $excel = PCWExporterService::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
    }
}
