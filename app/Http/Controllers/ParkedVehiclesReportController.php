<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\Utils\Geolocation;
use App\ParkingReport;
use App\Services\PCWExporter;
use Auth;
use Illuminate\Http\Request;
use Route;

class ParkedVehiclesReportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.vehicles.parked.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchReport(Request $request)
    {
        $stringParams = explode('?', $request->getRequestUri())[1] ?? '';
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehicles = $company->activeVehicles;
        $dateReport = $request->get('date-report');

        $parkedReports = ParkingReport::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereBetween('date', ["$dateReport 00:00:00", "$dateReport 23:59:59"])
            ->get();

        if ($request->get('export')) $this->export($parkedReports, $dateReport);

        $parkedReportsByVehicles = $parkedReports->groupBy('vehicle_id');

        return view('reports.vehicles.parked.parkedReport', compact(['parkedReportsByVehicles', 'dateReport', 'stringParams']));
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
            $route = $dispatchRegister->route ?? null;
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                       # A CELL
                __('Date') => $parkedReport->date,                                       # B CELL
                __('Vehicle') => intval($vehicle->number),                               # C CELL
                __('Plate') => $vehicle->plate,                                          # D CELL
                __('Route') => $route->name ?? __('Without assigned route'),        # E CELL
                __('Status') => $parkedReport->timed,                                    # F CELL
            ];
        }

        PCWExporter::excel([
            'fileName' => __('Parked Report') . " $dateReport",
            'title' => __('Parked Report') . " $dateReport",
            'subTitle' => __('Parked Report'),
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
        return Geolocation::getAddressFromCoordinates($parkingReport->latitude, $parkingReport->longitude);
    }

    /**
     * @param ParkingReport $parkingReport
     * @return mixed
     */
    public function getImageFromCoordinate(ParkingReport $parkingReport)
    {
        $route = $parkingReport->dispatchRegister->route;
        return Geolocation::getImageRouteWithANearLocation($route, $parkingReport);
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
                $routes = $company != 'null' ? Route::where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('reports.route.off-road.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
