<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Auth;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function loadSelectRoutes(Request $request)
    {
        $routes = null;
        $company = $this->getCompany($request);
        $vehicle = Vehicle::find($request->get('vehicle'));
        $date = $request->get('date');
        $withAll = $request->get('with-all');

        if ($company) $routes = self::getRoutesFromCompany();
        else if ($vehicle && $date) $routes = self::getRoutesFromVehicleAndDate($vehicle, $date);

        return view('partials.selects.routes', compact(['routes', 'withAll']));
    }

    public function loadSelectRouteRoundTrips(Request $request)
    {
        $routeId = $request->get('route');
        $vehicleId = $request->get('vehicle');
        $date = $request->get('date');

        $dispatchRegisters = DispatchRegister::findAllByDateAndVehicleAndRoute($date, $vehicleId, $routeId);

        return view('partials.selects.round-trips', compact('dispatchRegisters'));
    }

    public function loadSelectVehicles(Request $request)
    {
        $vehicles = self::getVehiclesFromCompany($this->getCompany($request));
        return view('partials.selects.vehicles', compact('vehicles'));
    }

    /**
     * @param Request $request
     * @return Company|Company[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public static function getCompany(Request $request)
    {
        $requestCompany = $request->get('company') ?? $request->get('company-report');
        return (Auth::user()->isAdmin() && $requestCompany ? Company::find($requestCompany) : Auth::user()->company);
    }

    public static function getRoutesFromVehicleAndDate(Vehicle $vehicle, $date)
    {
        $dispatchRegisters = DispatchRegister::completed()->where('date', $date)->where('vehicle_id', $vehicle->id)->get();
        if ($dispatchRegisters->isNotEmpty()) {
            $routes = collect([]);
            foreach ($dispatchRegisters as $dispatchRegister) {
                if ($dispatchRegister->route) {
                    $routes->put($dispatchRegister->route->id, $dispatchRegister->route);
                }
            }
            return $routes->sortBy(function ($r, $key) {
                return $r->name;
            });
        }
        return null;
    }

    public static function getRoutesFromCompany(Company $company = null)
    {
        return ($company ? $company->activeRoutes->sortBy('name') : []);
    }

    public static function getVehiclesFromCompany(Company $company = null)
    {
        return ($company ? $company->activeVehicles->sortBy('number') : []);
    }
}
