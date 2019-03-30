<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Services\PCWExporterService;
use App\Services\Reports\Routes\ControlPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlPointsReportController extends Controller
{
    /**
     * @var ControlPointService
     */
    private $controlPointService;
    /**
     * @var GeneralController
     */
    private $generalController;

    /**
     * ControlPointsReportController constructor.
     * @param ControlPointService $controlPointService
     * @param GeneralController $generalController
     */
    public function __construct(ControlPointService $controlPointService, GeneralController $generalController)
    {
        $this->controlPointService = $controlPointService;
        $this->generalController = $generalController;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.route.control-points.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchReport(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $dateReport = $request->get('date-report');
        $route = Route::find($request->get('route-report'));
        $typeReport = $request->get('type-report');

        $query = (object)[
            'dateReport' => $dateReport,
            'typeReport' => $typeReport,
            'company' => $company,
            'route' => $route
        ];

        if (!$route || !$route->belongsToCompany($company)) abort(404);

        $reportsByControlPoints = $this->controlPointService->buildReportsByControlPoints($route, $dateReport);

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
            foreach ($reportsByControlPoint as $reportByControlPoint){
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
