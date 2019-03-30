<?php

namespace App\Http\Controllers;

use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
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
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;

    /**
     * @var SpeedingService
     */
    private $speedingService;
    /**
     * @var GeneralController
     */
    private $generalController;

    /**
     * SpeedingReportController constructor.
     *
     * @param PCWAuthService $pcwAuthService
     * @param SpeedingService $speedingService
     * @param GeneralController $generalController
     */
    public function __construct(PCWAuthService $pcwAuthService, SpeedingService $speedingService, GeneralController $generalController)
    {
        $this->speedingService = $speedingService;
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

        return view('reports.vehicles.speeding.index', compact(['companies', 'routes']));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function show(Request $request)
    {
        $stringParams = explode('?', $request->getRequestUri())[1] ?? '';
        $company = $this->generalController->getCompany($request);
        $routeReport = $request->get('route-report');
        $dateReport = $request->get('date-report');

        $allSpeeding =$this->speedingService->allSpeeding($company, $dateReport, $routeReport);
        $speedingReportByVehicles = $this->speedingService->speedingByVehicles($allSpeeding);

        if( $request->get('export') )$this->export($speedingReportByVehicles,$dateReport, $request->get('type-report'));

        return view('reports.vehicles.speeding.show', compact(['speedingReportByVehicles', 'stringParams']));
    }


    /**
     * @param $speedingReportByVehicle
     * @param $dateReport
     * @param $typeReport
     * @throws \Exception
     */
    public function export($speedingReportByVehicle, $dateReport, $typeReport)
    {
        if( $typeReport == 'group' ){
            Excel::create(__('Speeding') . " $dateReport", function ($excel) use ($speedingReportByVehicle, $dateReport) {
                foreach ($speedingReportByVehicle as $speedingReport) {
                    $vehicle = $speedingReport->first()->vehicle;
                    $dataExcel = array();

                    foreach ($speedingReport as $speeding) {
                        $speed = $speeding->speed;
                        if( $speed > 200 ){
                            $speed = 100 + (random_int(-10,10));
                        }

                        $dataExcel[] = [
                            __('N°') => count($dataExcel) + 1,                             # A CELL
                            __('Time') => $speeding->time->toTimeString(),                                 # B CELL
                            __('Vehicle') => intval($vehicle->number),                     # C CELL
                            __('Plate') => $vehicle->plate,                                # D CELL
                            __('Speed') => number_format($speed,2, ',', ''),# E CELL
                            __('Address') => Geolocation::getAddressFromCoordinates($speeding->latitude, $speeding->longitude)# E CELL
                        ];
                    }

                    $dataExport = (object)[
                        'fileName' => __('Speeding') . " $dateReport",
                        'title' => __('Speeding') . " $dateReport",
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
        }else{
            $speedingReport = $speedingReportByVehicle->collapse();

            $dataExcel = array();

            foreach ($speedingReport as $speeding) {
                $vehicle = $speeding->vehicle;
                $speed = $speeding->speed;
                if( $speed > 200 ){
                    $speed = 100 + (random_int(-10,10));
                }

                $dataExcel[] = [
                    __('N°') => count($dataExcel) + 1,                             # A CELL
                    __('Time') => $speeding->time->toTimeString(),                 # C CELL
                    __('Vehicle') => $vehicle->number,                             # B CELL
                    __('Plate') => $vehicle->plate,                                # D CELL
                    __('Speed') => number_format($speed,2, ',', ''),# E CELL
                    __('Address') => Geolocation::getAddressFromCoordinates($speeding->latitude, $speeding->longitude)# E CELL
                ];
            }

            $fileData = (object)[
                'fileName' => __('Speeding') . " $dateReport",
                'title' => " $dateReport",
                'subTitle' => count($speedingReport)." ".__('Speeding'),
                'sheetTitle' => __('Speeding') . " $dateReport",
                'data' => $dataExcel
            ];
            //foreach ()
            /* SHEETS */

            PCWExporterService::excel($fileData);
        }
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
}
