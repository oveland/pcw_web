<?php

namespace App\Http\Controllers;

use App\Company;
use App\Route;
use Auth;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function loadSelectRoutes(Request $request)
    {
        $routes = self::getRoutesFromCompany($this->getCompany($request));
        return view('partials.selects.routes', compact('routes'));
    }

    public function loadSelectVehicles(Request $request)
    {
        $vehicles = self::getVehiclesFromCompany($this->getCompany($request));
        return view('partials.selects.vehicles', compact('vehicles'));
    }

    public static function getCompany(Request $request)
    {
        return (Auth::user()->isAdmin() ? Company::find($request->get('company')) : Auth::user()->company);
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
