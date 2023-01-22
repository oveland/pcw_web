<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\StrTime;
use App\Models\Vehicles\LastLocation;
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
use function foo\func;

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
     * @return Factory|View
     */
    public function ts()
    {
        $accessProperties = $this->authService->getAccessProperties();
        $companies = $accessProperties->companies;
        $vehicles = $accessProperties->vehicles;
        return view('reports.route.route.ts', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function show(Request $request)
    {
        $dateTimeRequest = collect(explode(' ', $request->get('date-report')));
        $dateTimeEndRequest = collect(explode(' ', $request->get('date-end-report')));

        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $dateTimeRequest->get(0);
        $withEndDate = $request->get('with-end-date');
        $dateEndReport = $withEndDate ? $dateTimeEndRequest->get(0) : null;
        $typeReport = $request->get('type-report');
        $routeReport = $request->get('route-report');
        $vehicleReport = $request->get('vehicle-report');
        $completedTurns = $request->get('completed-turns');
        $activeTurns = $request->get('active-turns');
        $cancelledTurns = $request->get('cancelled-turns');
        $noTakenTurns = $request->get('no-taken-turns');

        $timeReport = $request->get('time-range-report');
        //$timeRange = collect(explode(';', $timeReport));
        //$initialTime = $timeRange->get(0);
        //$finalTime = $timeRange->get(1);

        $initialTime = $dateTimeRequest->get(1);
        $finalTime = $dateTimeEndRequest->get(1);

        $onlyLastLap = $request->get('last-laps');

        if ($routeReport == 'none') return $this->showReportWithOutRoute($request);

        $dispatchRegistersByVehicles = $this->routeService->dispatch->allByVehicles($company, $dateReport, $dateEndReport, $routeReport, $vehicleReport, $completedTurns, $noTakenTurns, $initialTime, $finalTime, $activeTurns, $cancelledTurns);

        if ($onlyLastLap) {
            $dispatchRegistersByVehicles = $dispatchRegistersByVehicles->mapWithKeys(function ($drs, $vehicleId) {
                return [$vehicleId => collect([$drs->last()])];
            });
            $typeReport = 'ungroup';
        }

        $reportsByVehicle = collect([]);
        foreach ($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle) {
            $reportsByVehicle->put($vehicleId, CounterByRecorder::reportByVehicle($vehicleId, $dispatchRegistersByVehicle));
        }

        switch ($typeReport) {
            case 'group-vehicles':
                if ($request->get('export')) $this->routeService->getExporter($company)->groupedRouteReport($dispatchRegistersByVehicles, $dateReport);
                $view = 'reports.route.route.routeReportByVehicle';
                break;
            default:
                if ($request->get('export')) $this->routeService->getExporter($company)->ungroupedRouteReport($dispatchRegistersByVehicles, $dateReport);
                $view = 'reports.route.route.routeReportByAll';
                break;

        }

        return view($view, compact([
            'dispatchRegistersByVehicles',
            'reportsByVehicle',
            'company',
            'dateReport',
            'dateEndReport',
            'withEndDate',
            'routeReport',
            'vehicleReport',
            'typeReport',
            'completedTurns',
            'activeTurns',
            'cancelledTurns',
            'timeReport'
        ]));
    }

    public function showReportWithOutRoute(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $request->get('date-report');
//        $thresholdKm = $request->get('threshold-km');
        $vehicleReport = $request->get('vehicle-report');
//        $completedTurns = $request->get('completed-turns');
        $timeRange = collect(explode(';', $request->get('time-range-report')));
        $initialTime = $timeRange->get(0);
        $finalTime = $timeRange->get(1);

        if ($vehicleReport && $vehicleReport != 'all') $vehiclesId = [$vehicleReport];
        else $vehiclesId = $company->activeVehicles->pluck('id');

        $from = Carbon::now();

        $locations = Location::forDate($dateReport)->with(['vehicle', 'dispatchRegister'])
            ->whereBetween('date', ["$dateReport $initialTime:00", "$dateReport $finalTime:59"])
            ->whereIn('vehicle_id', $vehiclesId)
            ->get()
            ->filter(function (Location $l) {
                return !$l->dispatchRegister || !$l->dispatchRegister->isActive();
            });

//        dd(Carbon::now()->diffAsCarbonInterval($from)->forHumans());

        $dispatchRegisters = DispatchRegister::where('date', '=', $dateReport)
            ->whereBetween('departure_time', ["$initialTime:00", "$finalTime:00"])
            ->whereIn('vehicle_id', $locations->pluck('vehicle_id'))
            ->active()
            ->orderBy('departure_time')
            ->get();

        $locations = $locations->whereNotIn('vehicle_id', $dispatchRegisters->pluck('vehicle_id'));

        $from1 = Carbon::now();

        $vehiclesData = $locations->groupBy('vehicle_id')->mapWithKeys(function ($l, $vehicleId) {
            $l = collect($l)->sortBy('date');
            $first = $l->first();
            $last = $l->last();
            $kmInTimeRange = $last->current_mileage - $first->current_mileage;

            return [$vehicleId => (object)compact(['first', 'last', 'kmInTimeRange'])];
        });

//        dd('locas > ' ,Carbon::now()->diffAsCarbonInterval($from1)->forHumans())

        $vehiclesData = $vehiclesData->filter(function ($d) {
            return $d->kmInTimeRange >= 1000;
        });

        $timeRange = (object)[
            'initial' => intval(round(StrTime::toSeg($initialTime) / 5)),
            'final' => intval(round(StrTime::toSeg($finalTime) / 5)),
        ];

        return view('reports.route.route.withOutRoute', compact(['vehiclesData', 'company', 'dateReport', 'timeRange']));
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
        if ($dispatchRegister->locations()->count() < 100) { // TODO: While fix bug on chart map view when report loads fast
            sleep(2);
        }
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
