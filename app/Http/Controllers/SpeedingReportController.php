<?php

namespace App\Http\Controllers;

use App\Exports\SpeedingExport;
use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\SpeedingService;
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
     * @var PCWExporterService
     */
    private $pcwExporterService;

    /**
     * SpeedingReportController constructor.
     *
     * @param PCWAuthService $pcwAuthService
     * @param SpeedingService $speedingService
     * @param PCWExporterService $pcwExporterService
     */
    public function __construct(PCWAuthService $pcwAuthService, SpeedingService $speedingService, PCWExporterService $pcwExporterService)
    {
        $this->speedingService = $speedingService;
        $this->pcwAuthService = $pcwAuthService;
        $this->pcwExporterService = $pcwExporterService;
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

        return view('reports.vehicles.speeding.index', compact(['companies', 'routes', 'vehicles']));
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

        if ($request->get('export')) return $this->pcwExporterService->exportSpeeding($speedingReportByVehicles);

        return view('reports.vehicles.speeding.show', compact(['speedingReportByVehicles', 'query']));

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
