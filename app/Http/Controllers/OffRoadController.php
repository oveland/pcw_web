<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\Utils\Geolocation;
use App\OffRoad;
use App\Route;
use App\Services\PCWExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Excel;

class OffRoadController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.route.off-road.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchReport(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehicles = $company->activeVehicles;
        $dateReport = $request->get('date-report');

        $allOffRoads = OffRoad::validCoordinates()
            ->whereBetween('date', [$dateReport . ' 00:00:00', $dateReport . ' 23:59:59'])
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->orderBy('date')
            ->get();

        switch ($request->get('type-report')) {
            case 'vehicle':
                $allOffRoadsByVehicles = collect($allOffRoads->groupBy('vehicle_id'));
                $offRoadsByVehicles = array();
                foreach ($allOffRoadsByVehicles as $vehicleId => $offRoadsByVehicle) {
                    $recheckOffRoad = $this->dateLessThanNewOffRoadCalculateProcess($dateReport);
                    $offRoadsByVehicles[$vehicleId] = self::groupByFirstOffRoad($offRoadsByVehicle, $recheckOffRoad);
                    if ($recheckOffRoad && count($offRoadsByVehicles[$vehicleId]) == 0) unset($offRoadsByVehicles[$vehicleId]); /* TODO: Temporal until 2018-03-16 */
                }

                //if ($request->get('export')) $this->export($offRoadsByVehicles,$dateReport);

                return view('reports.route.off-road.offRoadByVehicle', compact(['offRoadsByVehicles','dateReport','company']));
                break;
            case 'route':
                //$offRoadsByVehicle = $allOffRoads->groupBy('dispatch_register_id');
                return view('reports.route.off-road.offRoadByRoute', compact('offRoadsByVehicles'));
                break;
        }

        return redirect(route('report-route-off-road-index'));
    }

    /**
     * Export report to excel format
     *
     * @param $offRoadsByVehicles
     */
    public function export($offRoadsByVehicles,$dateReport)
    {

        dd($offRoadsByVehicles);

        Excel::create(__('Off road report')." $dateReport", function ($excel) use ($offRoadsByVehicles) {
            foreach ($offRoadsByVehicles as $offRoadsByVehicle){
                $dataExport = array();
                //foreach ()
                /* SHEETS */
                $excel = PCWExporter::createHeaders($excel,$dataExport);
                $excel = PCWExporter::createSheet($excel, $dataExport);
                $dataExport = (object)$dataExport;
            }
        })->export('xlsx');

    }

    /**
     * Temporal function **** Delete on date greater than 2018-03-16 (Because data locations are saved only for 6 months)
     *
     * @param $dateReport
     * @return bool
     */
    public function dateLessThanNewOffRoadCalculateProcess($dateReport)
    {
        return ($dateReport <= '2017-09-16');
    }

    /**
     * @param $offRoads
     * @param $recCheckOffRoad
     * @return array
     */
    public
    static function groupByFirstOffRoad($offRoads, $recCheckOffRoad = false)
    {
        if (!count($offRoads)) return collect([]);

        $offRoadsReport = array();
        $prevOffRoad = null;

        foreach ($offRoads as $offRoad) {
            if ($prevOffRoad) {
                if ($offRoad->date->diff($prevOffRoad->date)->format('%H:%I:%S') > '00:05:00') {
                    $offRoadsReport[] = $offRoad;
                }
            } else {
                $offRoadsReport[] = $offRoad;
            }
            $prevOffRoad = $offRoad;
        }

        $offRoadsReport = collect($offRoadsReport)
            ->sortBy(function ($offRoad, $key) {
                return $offRoad->dispatchRegister->route->name;
            })
            ->groupBy(function ($offRoad, $key) {
                return $offRoad->dispatchRegister->route->id;
            });

        if ($recCheckOffRoad) {
            foreach ($offRoadsReport as $routeId => $offRoadReport) {
                $route = Route::find($routeId);
                $routeCoordinates = RouteReportController::getRouteCoordinates($route->url);
                $checkedOffRoadReport = array();
                foreach ($offRoadReport as $offRoad) {
                    if (RouteReportController::checkOffRoad($offRoad, $routeCoordinates)) {
                        $checkedOffRoadReport[] = $offRoad;
                    }
                }
                if (count($checkedOffRoadReport) > 0) $offRoadsReport[$routeId] = $checkedOffRoadReport;
                else unset($offRoadsReport[$routeId]);
            }
        }

        return $offRoadsReport;
    }

    /**
     * @param OffRoad $offRoad
     * @return mixed
     */
    public function getAddressFromCoordinates(OffRoad $offRoad)
    {
        sleep(1); // Because google (Free layer) only lets 50 request/second
        return Geolocation::getAddressFromCoordinates($offRoad->latitude, $offRoad->longitude);
    }

    /**
     * @param OffRoad $offRoad
     * @return mixed
     */
    public function getImageFromCoordinate(OffRoad $offRoad)
    {
        $route = $offRoad->dispatchRegister->route;
        return Geolocation::getImageRouteWithANearLocation($route, $offRoad);
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
