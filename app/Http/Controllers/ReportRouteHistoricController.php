<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use App\Services\PCWExporterService;
use Auth;
use Illuminate\Http\Request;

class ReportRouteHistoricController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $companies = Company::active()->get();
        }else{
            $routes = $user->company->routes;
            $vehicles = $user->assignedVehicles();
        }
        return view('reports.route.historic.index', compact(['companies', 'vehicles', 'routes']));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $vehicleReport = $request->get('vehicle-report');
        $forExport = $request->get('export');
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $report = $this->buildHistoric($dateReport, $vehicleReport, $initialTime, $finalTime, $forExport);

        if ($forExport) $this->export($report);
        $report->exportLink = $request->getRequestUri() . "&export=true";

        return response()->json($report);
    }

    /**
     * @param $dateReport
     * @param $vehicleReport
     * @param $initialTime
     * @param $finalTime
     * @param bool $withAddress
     * @return object
     */
    public function buildHistoric($dateReport, $vehicleReport, $initialTime, $finalTime, $withAddress = false)
    {
        $vehicle = Vehicle::find($vehicleReport);

        $locations = Location::whereBetween('date', ["$dateReport $initialTime", "$dateReport $finalTime"])
            ->with('vehicle')
            ->with('dispatchRegister')
            ->with('vehicleStatus')
            ->where('vehicle_id', $vehicleReport)
            ->orderBy('date')
            ->get();

        $dataLocations = collect([]);

        foreach ($locations as $location) {
            $dispatchRegister = $location->dispatchRegister;
            $dataLocations->push((object)[
                'time' => $location->date->format('H:i:s'),
                'date' => $location->date->format('Y-m-d'),
                'currentMileage' => number_format(intval($location->current_mileage) / 1000, 2, '.', ''),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'address' => $location->getAddress($withAddress),
                'odometer' => $location->odometer,
                'orientation' => $location->orientation,
                'speed' => $location->speed,
                'speeding' => $location->speeding,
                'vehicleStatus' => (object)[
                    'status' => $location->vehicleStatus->des_status,
                    'iconClass' => $location->vehicleStatus->icon_class,
                    'mainClass' => $location->vehicleStatus->main_class,
                ],
                'dispatchRegister' => $location->dispatchRegister ? $location->dispatchRegister->getAPIFields() : null,
                'vehicle' => $location->vehicle->getAPIFields()
            ]);
        }

        $totalLocations = $dataLocations->count();

        $report = (object)[
            'dateReport' => $dateReport,
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'vehicle' => $vehicle->getAPIFields(),
            'historic' => $dataLocations,
            'total' => $totalLocations,
            'from' => $totalLocations ? $dataLocations->first()->time : '--:--',
            'to' => $totalLocations ? $dataLocations->last()->time : '--:--',
        ];

        return $report;
    }

    /**
     * @param $report
     * @throws \Exception
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
                __('Vehicle status') => $location->vehicleStatus ? $location->vehicleStatus->status:'...',                                           # F CELL
                __('Address') => $location->address,                                                                # G CELL
                __('Info route') => $infoRoute                                                                      # H CELL
            ];
        }

        $fileData = (object)[
            'fileName' => __('Speeding') . " $report->dateReport",
            'title' => __('Historic') . " $report->dateReport - #" . $report->vehicle->number,
            'subTitle' => __('Time') . " $report->initialTime - $report->finalTime ",
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

        if ($dispatchRegister){
            $route = $dispatchRegister->route;
            $infoDispatchRegister = "$route->name \n ".__('Round trip')." $dispatchRegister->round_trip \n ".__('Turn')." $dispatchRegister->turn \n ".__('Dispatched')." $dispatchRegister->departure_time \n ".__('Driver')." $dispatchRegister->driver_name";
        }

        return $infoDispatchRegister;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('partials.selects.routes', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
