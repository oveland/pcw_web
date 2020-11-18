<?php


namespace App\Services\Auth;

use App\Http\Controllers\GeneralController;
use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PCWAuthService
{
    /**
     * @var GeneralController
     */
    private $generalController;

    /**
     * @var User
     */
    public $user;


    public function __construct(GeneralController $generalController)
    {
        $this->generalController = $generalController;
    }

    /**
     * @param Company|null $company
     * @return object
     */
    public function access(Company $company = null)
    {
        return $this->getAccessProperties($company);
    }

    /**
     * @param Company|null $company
     * @return object
     */
    public function getAccessProperties(Company $company = null)
    {
        $this->user = Auth::user();
        $company = $this->user->isAdmin() && $company ? $company : $this->user->company;

        return (object)[
            'company' => $company,
            'companies' => $this->user->isAdmin() ? Company::active()->get() : collect([]),
            'drivers' => $company->activeDrivers,
            'routes' => $this->generalController->getRoutesFromCompany($company),
            'vehicles' => $this->user->assignedVehicles($company)
        ];
    }

    /**
     * @param Request $request
     * @return Company|null
     */
    public function getCompanyFromRequest(Request $request)
    {
        return $this->generalController->getCompany($request);
    }

    /**
     * @param Request $request
     * @return Vehicle|null
     */
    public function getVehicleFromRequest(Request $request)
    {
        $company = $this->getCompanyFromRequest($request);
        $vehicle = null;
        $requestVehicle = $request->get('vehicle') ?? $request->get('vehicle-report');
        if ($requestVehicle) {
            $vehicle = Vehicle::find($requestVehicle);
            if (!$vehicle->belongsToCompany($company)) abort(302);
        }

        return $vehicle;
    }

    /**
     * @param Request $request
     * @return Route|null
     */
    public function getRouteFromRequest(Request $request)
    {
        $routeRequest = $request->get('route-report');
        $route = $routeRequest == "all" ? null : Route::find($routeRequest);

        $company = $this->getCompanyFromRequest($request);
        if ($route && $company && !$route->belongsToCompany($company)) abort(404);

        return $route;
    }

    /**
     * @param Request $request
     * @return Route[]|Collection
     */
    public function getRoutesFromRequest(Request $request)
    {
        $routeRequest = $request->get('route-report');
        $company = $this->getCompanyFromRequest($request);
        $route = $this->getRouteFromRequest($request);

        return $route ? $route->subRoutes : ($routeRequest == 'all' ? $company->activeRoutes : collect([]));
    }
}