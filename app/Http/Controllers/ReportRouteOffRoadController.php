<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;
use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\OffRoadService;
use DB;
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
        $vehicles = $access->vehicles;

        return view('reports.route.off-road.index', compact(['companies', 'routes', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function searchReport(Request $request)
    {
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $date = $request->get('date-report');
        $dateEnd = $request->get('with-end-date') ? $request->get('date-end-report') : $date;

        $query = (object)[
            'stringParams' => explode('?', $request->getRequestUri())[1] ?? '',
            'company' => $this->pcwAuthService->getCompanyFromRequest($request),
            'dateReport' => $date,
            'dateEndReport' => $dateEnd,
            'routeReport' => $request->get('route-report'),
            'vehicleReport' => $request->get('vehicle-report'),
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'typeReport' => $request->get('type-report'),
        ];

        $allOffRoads = $this->offRoadService->allOffRoads($query->company, "$query->dateReport $query->initialTime:00", "$query->dateEndReport $query->finalTime:59", $query->routeReport, $query->vehicleReport);
        $offRoadsByVehicles = $this->offRoadService->offRoadsByVehicles($allOffRoads);
        
        if ($request->get('export')) $this->offRoadService->exportByVehicles($offRoadsByVehicles, $query);

        return view('reports.route.off-road.offRoadByVehicle', compact(['offRoadsByVehicles', 'query']));
    }

    /**
     * @param Location $location
     * @return mixed
     */
    public function getAddressFromCoordinates(Location $location)
    {
        return $location->getAddress(false, true);
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

        DB::statement("UPDATE locations_0 SET status = 'FOR' WHERE id = $location->id");
        DB::statement("REFRESH MATERIALIZED VIEW locations_1");
    }
}