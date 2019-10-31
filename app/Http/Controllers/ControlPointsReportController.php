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
        $vehicleId = $request->get('vehicle-report');
        $vehicle = $vehicleId != 'all' ? Vehicle::find($request->get('vehicle-report')) : null;
        $route = Route::find($request->get('route-report'));

        if ($route->as_group) {
            return view('partials.alerts.unableRouteControlPoint');
        }

        $typeReport = $request->get('type-report');

        $query = (object)[
            'dateReport' => $dateReport,
            'typeReport' => $typeReport,
            'company' => $company,
            'route' => $route,
            'vehicleId' => $vehicleId,
            'vehicle' => $vehicle
        ];

        if (!$route || !$route->belongsToCompany($company)) abort(404);

        $reportsByControlPoints = $this->controlPointService->buildReportsByControlPoints($route, $vehicle, $dateReport);

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
            $driver = $report->driver;

            $dispatchRegister = $report->dispatchRegister;
            $reportsByControlPoint = $report->reportsByControlPoint;

            $routeTimes = '--:--:--';
            if ($dispatchRegister->complete()) {
                $routeTimes = "Salida: $dispatchRegister->departure_time\nLlegada: $dispatchRegister->arrival_time\nEn ruta: " . $dispatchRegister->getRouteTime();
            }

            $controlPointsReport = collect([]);
            foreach ($reportsByControlPoint as $reportByControlPoint) {
                $controlPoint = $reportByControlPoint->controlPoint;
                $controlPointsReport->put($controlPoint->name, "$reportByControlPoint->difference\n$reportByControlPoint->statusText");
            }

            $dataExcel[] = collect([
                __('N°') => count($dataExcel) + 1,                                          # A CELL
                __('Vehicle') => intval($vehicle->number),                                  # B CELL
                __('Plate') => $vehicle->plate,                                             # C CELL
                __('Driver') => $driver ? $driver->fullName() : __('Not assigned'),    # D CELL
                __('Route time') => $routeTimes,                                            # E CELL
            ])->merge($controlPointsReport)->toArray();
        }

        $fileData = [
            'fileName' => __('Control Points') . " $route->name $dateReport",
            'title' => __('Control point time report'),
            'subTitle' => " $route->name $dateReport",
            'data' => $dataExcel,
            'type' => 'controlPointTimesByAll'
        ];


        return PCWExporterService::excel($fileData);
    }
}
