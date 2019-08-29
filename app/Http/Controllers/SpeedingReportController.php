<?php

namespace App\Http\Controllers;

use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\SpeedingService;
use Excel;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\Geolocation;
use App\Services\PCWExporterService;
use Auth;
use Illuminate\View\View;
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
     * SpeedingReportController constructor.
     *
     * @param PCWAuthService $pcwAuthService
     * @param SpeedingService $speedingService
     */
    public function __construct(PCWAuthService $pcwAuthService, SpeedingService $speedingService)
    {
        $this->speedingService = $speedingService;
        $this->pcwAuthService = $pcwAuthService;
    }


    /**
     * @return Factory|View
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
     * @return Factory|View
     * @throws Exception
     */
    public function show(Request $request)
    {
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $query = (object)[
            'stringParams' => explode('?', $request->getRequestUri())[1] ?? '',
            'company' => $this->pcwAuthService->getCompanyFromRequest($request),
            'dateReport' => $request->get('date-report'),
            'routeReport' => $request->get('route-report'),
            'vehicleReport' => $request->get('vehicle-report'),
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'typeReport' => $request->get('type-report'),
        ];

        $allSpeeding =$this->speedingService->allSpeeding($query->company, "$query->dateReport $query->initialTime:00", "$query->dateReport $query->finalTime:59", $query->routeReport, $query->vehicleReport);
        $speedingReportByVehicles = $this->speedingService->speedingByVehicles($allSpeeding);

        if( $request->get('export') )$this->export($speedingReportByVehicles, $query);

        return view('reports.vehicles.speeding.show', compact(['speedingReportByVehicles', 'query']));
    }


    /**
     * @param $speedingReportByVehicle
     * @param $query
     * @throws Exception
     */
    public function export($speedingReportByVehicle, $query)
    {
        $dateReport = $query->dateReport;
        $typeReport = $query->typeReport;

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
