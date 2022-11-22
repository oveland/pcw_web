<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Http\Controllers\Utils\Geolocation;
use App\Models\Vehicles\ParkingReport;
use App\Services\Auth\PCWAuthService;
use App\Services\Exports\PCWExporterService;
use Auth;
use Illuminate\Http\Request;
use Route;

class ParkedVehiclesReportController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;

    /**
     * @var GeneralController
     */
    private $generalController;

    /**
     * ParkedVehiclesReportController constructor.
     * @param PCWAuthService $pcwAuthService
     * @param GeneralController $generalController
     */
    public function __construct(PCWAuthService $pcwAuthService, GeneralController $generalController)
    {
        $this->pcwAuthService = $pcwAuthService;
        $this->generalController = $generalController;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $access = $this->pcwAuthService->getAccessProperties();
        $companies = $access->companies;
        $routes = $access->routes;

        return view('reports.vehicles.parked.index', compact(['companies', 'routes']));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchReport(Request $request)
    {
        $stringParams = explode('?', $request->getRequestUri())[1] ?? '';
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehicleReport = $request->get('vehicle-report');
        $routeReport = $request->get('route-report');
        $dateReport = $request->get('date-report');
        $dateEnd = $request->get('with-end-date') ? $request->get('date-end-report') : $dateReport;

        $vehicles = $company->userVehicles($routeReport);
        if ($vehicleReport != 'all') {
            $vehicles = $vehicles->where('id', $vehicleReport);
        }

        $parkedReports = ParkingReport::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereBetween('date', ["$dateReport 00:00:00", "$dateEnd 23:59:59"])
            ->orderBy('id')
            ->with(['vehicle', 'dispatchRegister'])
            ->get();

        $dispatches = $company->dispatches;

        $parkedReportsFiltered = collect([]);
        foreach ($parkedReports->take(5) as $report) {
            $exclude = false;
            foreach ($dispatches as $dispatch) {
                $distanceToDispatch = Geolocation::getDistance($dispatch->latitude, $dispatch->longitude, $report->latitude, $report->longitude);
                if ($distanceToDispatch <= $dispatch->radio_geofence) {
                    $exclude = true;
                }
            }

            if (!$exclude) {
                $parkedReportsFiltered->push($report);
            }
        }

        $parkedReports = $parkedReportsFiltered->sortBy(function ($pr) {
            return $pr->vehicle->number . '-' . $pr->id;
        });

        if ($request->get('export')) $this->export($parkedReports, $dateReport);

        $parkedReportsByVehicles = $parkedReports->groupBy('vehicle_id');

        return view('reports.vehicles.parked.parkedReport', compact(['parkedReportsByVehicles', 'stringParams']));
    }

    /**
     * Export report to excel format
     *
     * @param $parkedReports
     * @param $dateReport
     */
    public function export($parkedReports, $dateReport)
    {
        $dataExcel = array();
        foreach ($parkedReports as $parkedReport) {
            $vehicle = $parkedReport->vehicle;
            $dispatchRegister = $parkedReport->dispatchRegister;
            $route = $dispatchRegister ? $dispatchRegister->route : null;
            $driverName = $dispatchRegister ? $dispatchRegister->driverName() : null;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                       # A CELL
                __('Parked date') => $parkedReport->date,                                # B CELL
                __('Vehicle') => intval($vehicle->number),                               # C CELL
                __('Route') => $route->name ?? __('Without assigned route'),        # E CELL
                __('Driver') => $driverName,                                             # F CELL
            ];
        }

        PCWExporterService::excel([
            'fileName' => __('Parked report') . " $dateReport",
            'title' => __('Parked report') . " $dateReport",
            'subTitle' => __('Parked report'),
            'data' => $dataExcel
        ]);
    }

    /**
     * @param ParkingReport $parkingReport
     * @return mixed
     */
    public function getAddressFromCoordinates(ParkingReport $parkingReport)
    {
        sleep(1); // Because google (Free layer) only lets 50 request/second
        return Geolocation::getAddressFromCoordinates($parkingReport->latitude, $parkingReport->longitude, true);
    }

    /**
     * @param ParkingReport $parkingReport
     * @return mixed
     */
    public function getImageFromCoordinate(ParkingReport $parkingReport)
    {
        $route = $parkingReport->dispatchRegister ? $parkingReport->dispatchRegister->route : null;
        return Geolocation::getImageRouteWithANearLocation($route, $parkingReport);
    }
}
