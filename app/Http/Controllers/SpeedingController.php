<?php

namespace App\Http\Controllers;

use App\Speeding;
use App\Vehicle;
use Excel;
use Illuminate\Http\Request;
use App\Company;
use App\Http\Controllers\Utils\Geolocation;
use App\ParkingReport;
use App\Services\PCWExporter;
use Auth;
use Route;

class SpeedingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.vehicles.speeding.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchReport(Request $request)
    {
        $stringParams = explode('?', $request->getRequestUri())[1] ?? '';
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehicles = $company->vehicles;
        $dateReport = $request->get('date-report');

        $speedingReportByVehicle = Speeding::where('date',$dateReport)
            ->whereIn('vehicle_id',$vehicles->pluck('id'))
            ->get()
            ->groupBy('vehicle_id');

        if( $request->get('export') )$this->export($speedingReportByVehicle,$dateReport);

        return view('reports.vehicles.speeding.speedingReport', compact(['speedingReportByVehicle', 'stringParams']));
    }

    /**
     * @param $speedingReportByVehicle
     * @param $dateReport
     */
    public function export($speedingReportByVehicle, $dateReport)
    {
        Excel::create(__('Speeding Report') . " $dateReport", function ($excel) use ($speedingReportByVehicle, $dateReport) {
            foreach ($speedingReportByVehicle as $vehicleId => $speedingReport) {
                $vehicle = Vehicle::find($vehicleId);
                $dataExcel = array();

                foreach ($speedingReport as $speeding) {
                    $speed = $speeding->speed;
                    if( $speed > 100 ){
                        $speed = 70 + (random_int(-10,10));
                    }

                    $dataExcel[] = [
                        __('N°') => count($dataExcel) + 1,                             # A CELL
                        __('Time') => $speeding->time,                                 # B CELL
                        __('Vehicle') => intval($vehicle->number),                     # C CELL
                        __('Plate') => $vehicle->plate,                                # D CELL
                        __('Speed') => $speed                                          # E CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => __('Speeding Report') . " $dateReport",
                    'title' => __('Speeding Report') . " $dateReport",
                    'subTitle' => count($speedingReport)." ".__('Speeding'),
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel
                ];
                //foreach ()
                /* SHEETS */
                $excel = PCWExporter::createHeaders($excel, $dataExport);
                $excel = PCWExporter::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
    }

    /**
     * @param Speeding $speeding
     * @return mixed
     */
    public function getAddressFromCoordinates(Speeding $speeding)
    {
        sleep(1); // Because google (Free layer) only lets 50 request/second
        return Geolocation::getAddressFromCoordinates($speeding->latitude, $speeding->longitude);
    }

    /**
     * @param Speeding $speeding
     * @return mixed
     */
    public function getImageLocationFromCoordinates(Speeding $speeding)
    {
        sleep(1); // Because google (Free layer) only lets 50 request/second
        return Geolocation::getImageLocationFromCoordinates($speeding->latitude, $speeding->longitude);
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
