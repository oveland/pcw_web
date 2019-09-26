<?php

namespace App\Http\Controllers;

use App\LastLocation;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Report;
use App\Models\Vehicles\Location;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\RouteService;
use App\Traits\CounterByRecorder;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportRouteController extends Controller
{
    /**
     * @var RouteService
     */
    private $routeService;
    /**
     * @var PCWAuthService
     */
    private $authService;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $authService
     * @param RouteService $routeService
     */
    public function __construct(PCWAuthService $authService, RouteService $routeService)
    {

        $this->routeService = $routeService;
        $this->authService = $authService;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $accessProperties = $this->authService->getAccessProperties();
        $companies = $accessProperties->companies;
        $vehicles = $accessProperties->vehicles;
        return view('reports.route.route.index', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $request->get('date-report');
        $typeReport = $request->get('type-report');
        $routeReport = $request->get('route-report');
        $vehicleReport = $request->get('vehicle-report');
        $completedTurns = $request->get('completed-turns');

        if ($routeReport == 'none') return $this->showReportWithOutRoute($request);

        $dispatchRegistersByVehicles = $this->routeService->dispatch->allByVehicles($company, $dateReport, $routeReport, $vehicleReport, $completedTurns);

        $reportsByVehicle = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $reportsByVehicle->put($vehicleId, CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegistersByVehicle));
        }

        switch ($typeReport) {
            case 'group-vehicles':
                if ($request->get('export')) $this->routeService->export->groupedRouteReport($dispatchRegistersByVehicles, $dateReport);
                $view = 'reports.route.route.routeReportByVehicle';
                break;
            default:
                if ($request->get('export')) $this->routeService->export->ungroupedRouteReport($dispatchRegistersByVehicles, $dateReport);
                $view = 'reports.route.route.routeReportByAll';
                break;

        }

        return view($view, compact(['dispatchRegistersByVehicles', 'reportsByVehicle', 'company', 'dateReport', 'routeReport', 'vehicleReport', 'typeReport', 'completedTurns']));
    }

    public function showReportWithOutRoute(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $request->get('date-report');
        $thresholdKm = $request->get('threshold-km');
        $typeReport = $request->get('type-report');
        $vehicleReport = $request->get('vehicle-report');
        $completedTurns = $request->get('completed-turns');

        if ($dateReport >= Carbon::now()->toDateString()) return view('partials.alerts.onlyPreviousDate');

        if($vehicleReport && $vehicleReport != 'all') $vehiclesId = [$vehicleReport];
        else $vehiclesId = $company->activeVehicles->pluck('id');

        $lastLocations = LastLocation::with('vehicle')
            ->whereBetween('date', ["$dateReport 00:00:00", "$dateReport 23:59:59"])
            ->whereIn('vehicle_id', $vehiclesId)
            ->where('current_mileage', '>', $thresholdKm * 1000)
            ->where('current_mileage', '<', 700 * 1000)
            ->get()->filter(function ($ll) {
                return $ll->gpsIsOK();
            });

        $dispatchRegisters = DispatchRegister::where('date', '=', $dateReport)
            ->whereIn('vehicle_id', $lastLocations->pluck('vehicle_id'))
            ->active($completedTurns)
            ->orderBy('departure_time')
            ->get();

        $lasLocationsOut = $lastLocations->whereNotIn('vehicle_id', $dispatchRegisters->pluck('vehicle_id'));

        return view('reports.route.route.withOutRoute', compact(['lasLocationsOut', 'company', 'dateReport']));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @param $locationId
     * @param Request $request
     * @return string
     */
    public function chartView(DispatchRegister $dispatchRegister, $locationId, Request $request)
    {
        return view('reports.route.route.general', compact(['dispatchRegister', 'locationId']));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @param Request $request
     * @return JsonResponse
     */
    public function chart(DispatchRegister $dispatchRegister, Request $request)
    {
        sleep(1);
        $centerOnLocation = ($request->get('centerOnLocation')) ? Location::find(($request->get('centerOnLocation'))) : null;
        return response()->json($this->routeService->dispatch->locationsReports($dispatchRegister, $centerOnLocation));
    }

    /**
     * Gets table logs for calculated reports
     *
     * @param DispatchRegister $dispatchRegister
     * @return Factory|View
     */
    public function getReportLog(DispatchRegister $dispatchRegister)
    {
        $reports = Report::where('dispatch_register_id', $dispatchRegister->id)
            ->with('location')
            ->orderBy('date')
            ->get();

        $locationsReports = $this->routeService->dispatch->locationsReports($dispatchRegister);

        return view('reports.route.route.templates._tableReportLog', compact(['reports', 'locationsReports']));
    }

    /**
     * @param Request $request
     * @return Factory|View|string
     * @throws GuzzleException
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                return (new GeneralController())->loadSelectRoutes($request);

                /*$company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('partials.selects.routes', compact('routes'));*/
                break;
            case 'executeDAR':
                ini_set('MAX_EXECUTION_TIME', 0);
                set_time_limit(0);
                $dispatchRegisterId = $request->get('dispatchRegisterId');

                $client = new Client();
                $response = $client->request('GET', config('gps.server.url') . "/autoDispatcher/processDispatchRegister/$dispatchRegisterId?sync=true", ['timeout' => 0]);

                return $response->getBody();
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
