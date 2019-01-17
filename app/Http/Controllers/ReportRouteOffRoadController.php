<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Http\Controllers\Utils\Geolocation;
use App\Models\Vehicles\Location;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use App\Services\PCWExporterService;
use App\Services\Reports\Routes\OffRoadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Excel;

class ReportRouteOffRoadController extends Controller
{
    private $offRoadService;

    /**
     * ReportRouteOffRoadController constructor.
     * @param OffRoadService $offRoadService
     */
    public function __construct(OffRoadService $offRoadService)
    {
        $this->offRoadService = $offRoadService;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name', 'asc')->get();
        }
        return view('reports.route.off-road.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchReport(Request $request)
    {
        $company = GeneralController::getCompany($request);
        $dateReport = $request->get('date-report');
        $typeReport = $request->get('type-report');

        $query = (object)[
            'company' => $company,
            'dateReport' => $dateReport,
            'typeReport' => $typeReport,
        ];

        $allOffRoads = $this->offRoadService->allOffRoads($company, $dateReport);

        switch ($typeReport) {
            case 'vehicle':
                $offRoadsByVehicles = $this->offRoadService->offRoadsByVehicles($allOffRoads);

                if( $request->get('export') )$this->exportByVehicles($offRoadsByVehicles, $query);

                return view('reports.route.off-road.offRoadByVehicle', compact(['offRoadsByVehicles','query']));
                break;
            case 'route':
                //$offRoadsByVehicle = $allOffRoads->groupBy('dispatch_register_id');
                return view('reports.route.off-road.offRoadByRoute', compact('offRoadsByVehicles'));
                break;
        }

        return redirect(route('report-route-off-road-index'));
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'loadRoutes':
                $company = Auth::user()->isAdmin() ? $request->get('company') : Auth::user()->company->id;
                $routes = $company != 'null' ? Route::active()->where('company_id', '=', $company)->orderBy('name', 'asc')->get() : [];
                return view('partials.selects.routes', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
