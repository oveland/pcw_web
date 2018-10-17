<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\Utils\Geolocation;
use App\Location;
use App\Route;
use App\Services\pcwserviciosgps\reports\routes\OffRoadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OffRoadController extends Controller
{
    private $offRoadService;

    /**
     * OffRoadController constructor.
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

        $allOffRoads = $this->offRoadService->allOffRoads($company, $dateReport);

        switch ($request->get('type-report')) {
            case 'vehicle':
                $offRoadsByVehicles = $this->offRoadService->offRoadsByVehicles($allOffRoads);
                return view('reports.route.off-road.offRoadByVehicle', compact(['offRoadsByVehicles','dateReport','company']));
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
