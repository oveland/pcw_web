<?php

namespace App\Http\Controllers;

use App\Models\Vehicles\Location;
use App\Services\Reports\Routes\SpeedingService;
use App\Models\Vehicles\Speeding;
use App\Models\Vehicles\Vehicle;
use Excel;
use Illuminate\Http\Request;
use App\Models\Company\Company;
use App\Http\Controllers\Utils\Geolocation;
use App\Services\PCWExporterService;
use Auth;
use Route;

class SpeedingReportController extends Controller
{
    private $speedingService;

    /**
     * SpeedingReportController constructor.
     *
     * @param SpeedingService $speedingService
     */
    public function __construct(SpeedingService $speedingService)
    {
        $this->speedingService = $speedingService;
    }


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
    public function show(Request $request)
    {
        $stringParams = explode('?', $request->getRequestUri())[1] ?? '';
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $dateReport = $request->get('date-report');

        $allSpeeding =$this->speedingService->allSpeeding($company, $dateReport);

        $speedingReportByVehicles = $this->speedingService->speedingByVehicles($allSpeeding);

        if( $request->get('export') )$this->export($speedingReportByVehicles,$dateReport);

        return view('reports.vehicles.speeding.show', compact(['speedingReportByVehicles', 'stringParams']));
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
                        __('Time') => $speeding->time->toTimeString(),                                 # B CELL
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
                $excel = PCWExporterService::createHeaders($excel, $dataExport);
                $excel = PCWExporterService::createSheet($excel, $dataExport);
            }
        })->
        export('xlsx');
    }

    /**
     * @param Location $location
     * @return mixed
     */
    public function getAddressFromCoordinates(Location $location)
    {
        return Geolocation::getAddressFromCoordinates($location->latitude, $location->longitude);
    }

    /**
     * @param Location $location
     * @return mixed
     */
    public function getImageLocationFromCoordinates(Location $location)
    {
        return Geolocation::getImageLocationFromCoordinates($location->latitude, $location->longitude);
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
                return view('reports.route.off-road.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
