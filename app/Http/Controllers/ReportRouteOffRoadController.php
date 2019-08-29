<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\OffRoadService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Excel;
use Illuminate\View\View;

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
     * ReportRouteOffRoadController constructor.
     * @param PCWAuthService $pcwAuthService
     * @param OffRoadService $offRoadService
     */
    public function __construct(PCWAuthService $pcwAuthService, OffRoadService $offRoadService)
    {
        $this->pcwAuthService = $pcwAuthService;
        $this->offRoadService = $offRoadService;
    }


    /**
     * @return Factory|View
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
     * @return Factory|View
     */
    public function searchReport(Request $request)
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

        $allOffRoads = $this->offRoadService->allOffRoads($query->company, "$query->dateReport $query->initialTime:00", "$query->dateReport $query->finalTime:59", $query->routeReport, $query->vehicleReport);
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
