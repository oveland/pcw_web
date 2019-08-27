<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Http\Controllers\Utils\Geolocation;
use App\Models\Vehicles\Location;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use App\Services\Reports\Routes\OffRoadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Excel;

class ReportRouteOffRoadController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;

    /**
     * @var OffRoadService
     */
    private $offRoadService;
    /**
     * @var GeneralController
     */
    private $generalController;

    /**
     * ReportRouteOffRoadController constructor.
     * @param PCWAuthService $pcwAuthService
     * @param OffRoadService $offRoadService
     * @param GeneralController $generalController
     */
    public function __construct(PCWAuthService $pcwAuthService, OffRoadService $offRoadService, GeneralController $generalController)
    {
        $this->pcwAuthService = $pcwAuthService;
        $this->offRoadService = $offRoadService;
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

        return view('reports.route.off-road.index', compact(['companies', 'routes']));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchReport(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $routeReport = $request->get('route-report');
        $vehicleReport = $request->get('vehicle-report');
        $dateReport = $request->get('date-report');
        $typeReport = $request->get('type-report');
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $query = (object)[
            'company' => $company,
            'dateReport' => $dateReport,
            'routeReport' => $routeReport,
            'vehicleReport' => $vehicleReport,
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'typeReport' => $typeReport,
        ];

        $allOffRoads = $this->offRoadService->allOffRoads($company, "$dateReport $initialTime:00", "$dateReport $finalTime:59", $routeReport, $vehicleReport);

        $offRoadsByVehicles = $this->offRoadService->offRoadsByVehicles($allOffRoads);

        if( $request->get('export') )$this->exportByVehicles($offRoadsByVehicles, $query);

        return view('reports.route.off-road.offRoadByVehicle', compact(['offRoadsByVehicles','query']));
    }

    /**
     * @param Location $location
     * @return mixed
     */
    public function getAddressFromCoordinates(Location $location)
    {
        sleep(1); // Because google (Free layer) only lets 50 request/second
        return Geolocation::getAddressFromCoordinates($location->latitude, $location->longitude);
    }

    /**
     * @param Location $location
     * @return mixed
     */
    public function getImageFromCoordinate(Location $location)
    {
        $route = $location->dispatchRegister->route;
        return Geolocation::getImageRouteWithANearLocation($route, $location);
    }

    /**
     * @param Location $location
     */
    public function markLocationAsFakeOffRoad(Location $location)
    {
        $location->status = 'FOR';
        $location->save();
    }

    /**
     * @param $dataReport
     * @param $query
     */
    public function exportByVehicles($dataReport, $query)
    {
        $this->offRoadService->exportByVehicles($dataReport, $query);
    }
}
