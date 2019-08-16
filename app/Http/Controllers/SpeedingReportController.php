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
use Illuminate\View\View;

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

        return view('reports.vehicles.speeding.index', compact(['companies', 'routes']));
    }

    /**
     * @param Request $request
     * @return View|SpeedingExport
     * @throws Exception
     */
    public function show(Request $request)
    {
        $company = $this->pcwAuthService->getCompanyFromRequest($request);
        $routeReport = $request->get('route-report');
        $dateReport = $request->get('date-report');
        $typeReport = $request->get('type-report');

        $speedingReport = $this->speedingService->buildSpeedingReport($company, $dateReport, $typeReport, $routeReport);

        if ($request->get('export')) return $this->pcwExporterService->exportSpeeding($speedingReport);

        return view('reports.vehicles.speeding.show', compact(['speedingReport']));
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
