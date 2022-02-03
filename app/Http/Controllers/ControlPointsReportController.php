<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use App\Services\Reports\Routes\ControlPointService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ControlPointsReportController extends Controller
{
    /**
     * @var ControlPointService
     */
    private $controlPointService;
    /**
     * @var PCWAuthService
     */
    private $authService;

    /**
     * ControlPointsReportController constructor.
     * @param ControlPointService $controlPointService
     * @param PCWAuthService $authService
     */
    public function __construct(ControlPointService $controlPointService, PCWAuthService $authService)
    {
        $this->controlPointService = $controlPointService;
        $this->authService = $authService;
    }


    /**
     * @return Factory|View
     */
    public function index()
    {
        $access = $this->authService->getAccessProperties();
        $companies = $access->companies;
        $vehicles = $access->vehicles;
        return view('reports.route.control-points.index', compact(['companies', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function searchReport(Request $request)
    {
        $company = $this->authService->getCompanyFromRequest($request);
        $dateReport = $request->get('date-report');
        $dateEndReport = $request->get('with-end-date') ? $request->get('date-end-report') : $dateReport;

        $controlPointsReport = $request->get('control-points-report');
        $fringeReport = $request->get('fringe-report');
        $vehicleId = $request->get('vehicle-report');
        $showDetails = $request->get('show-details');
        $vehicle = $vehicleId != 'all' ? Vehicle::find($request->get('vehicle-report')) : null;

        $ascendant = $request->get('ascendant');
        $paintProfile = $request->get('paint-profile');

        $route = Route::find($request->get('route-report'));

        if ($route->as_group) {
            return view('partials.alerts.unableRouteControlPoint');
        }

        $typeReport = $request->get('type-report');

        $query = (object)[
            'stringParams' => explode('?', $request->getRequestUri())[1] ?? '',
            'dateReport' => $dateReport,
            'typeReport' => $typeReport,
            'company' => $company,
            'route' => $route,
            'vehicleId' => $vehicleId,
            'vehicle' => $vehicle,
            'ascendant' => $ascendant,
            'paintProfile' => $paintProfile,
            'controlPointsReport' => $controlPointsReport,
            'fringeReport' => $fringeReport,
            'showDetails' => $showDetails,
        ];

        if (!$route || !$route->belongsToCompany($company)) abort(404);

        $reportsByControlPoints = $this->controlPointService->buildReportsByControlPoints($company, $route, $vehicleId, $dateReport, $dateEndReport, $controlPointsReport, $fringeReport, !!$ascendant);

        switch ($typeReport) {
            case 'round-trip':
                $controlPointTimeReportsByRoundTrip = $reportsByControlPoints->groupBy(function ($reportsByControlPoints) {
                    return $reportsByControlPoints->dispatchRegister->round_trip;
                });

                return view('reports.route.control-points.ControlPointTimesByRoundTrip', compact(['controlPointTimeReportsByRoundTrip', 'route', 'query']));
                break;
            case 'vehicle':
                $controlPointTimeReportsByVehicles = $reportsByControlPoints->groupBy(function ($reportsByControlPoints) {
                    return $reportsByControlPoints->vehicle->id;
                });
                return view('reports.route.control-points.ControlPointTimesByVehicle', compact(['controlPointTimeReportsByVehicles', 'route', 'query']));
                break;
            default:
                if ($request->get('export')) $this->exportControlPointTimesByAll($reportsByControlPoints, $query);

                return view('reports.route.control-points.ControlPointTimesByAll', compact(['reportsByControlPoints', 'route', 'query']));
                break;
        }
    }

    /**
     * Export and store report to excel format
     * returns tag
     *
     * @param $reportsByControlPoints
     * @param $query
     * @return string
     */
    function exportControlPointTimesByAll($reportsByControlPoints, $query)
    {
        $dateReport = $query->dateReport;
        $route = $query->route;

        $dataExcel = array();
        foreach ($reportsByControlPoints as $report) {
            $vehicle = $report->vehicle;

            $dispatchRegister = $report->dispatchRegister;
            $reportsByControlPoint = $report->reportsByControlPoint;

            $controlPointsReport = collect([]);
            foreach ($reportsByControlPoint as $reportByControlPoint) {
                $controlPoint = $reportByControlPoint->controlPoint;
                $controlPointsReport->put($controlPoint->name, "$reportByControlPoint->difference");
            }

            $link = route('link-report-route-chart-view', ['dispatchRegister' => $dispatchRegister->id, 'location' => 0]);
            $controlPointsReport->put(__('Details'), $link);

            $dataExcel[] = collect([
                __('N°') => count($dataExcel) + 1,                                          # A CELL
                __('Date') => $dispatchRegister->date,                                          # A CELL
                __('Vehicle') => $vehicle->number,                                  # B CELL
                __('Route') => $dispatchRegister->route->name,                                  # B CELL
                __('Round trip') => $dispatchRegister->round_trip,                                  # B CELL
                __('Driver') => $report->driverName,                                        # D CELL
                __('Departure time') => $dispatchRegister->departure_time,                                            # E CELL
                __('Arrival time') => $dispatchRegister->arrival_time,                                            # E CELL
                __('Route time') => $dispatchRegister->getRouteTime(),                                            # E CELL
            ])->merge($controlPointsReport)->toArray();
        }

        $fileData = [
            'fileName' => __('Control Points') . " $route->name $dateReport",
            'title' => __('Control point time report'),
            'subTitle' => " $route->name $dateReport",
            'data' => $dataExcel,
            'type' => 'controlPointsReport'
        ];


        return PCWExporterService::excel($fileData);
    }
}
