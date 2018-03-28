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
        $withAll = $request->get('with-all');
        return view('partials.selects.routes', compact(['routes','withAll']));
    }

    public function loadSelectRouteRoundTrips(Request $request)
    {
        $routeId = $request->get('route');
        $vehicleId = $request->get('vehicle');
        $date = $request->get('date');

        $query = "
            SELECT max(rd.id_registro) AS dispatch_register_id, rd.n_vuelta last_round_trip
            FROM registrodespacho rd
              JOIN crear_vehiculo cv ON (cv.placa = (rd.n_placa)::text)
            WHERE rd.fecha = ('$date')::DATE AND (rd.observaciones = 'En camino' OR rd.observaciones = 'Terminó')
              AND cv.id_crear_vehiculo = $vehicleId
              AND rd.id_ruta = $routeId
              GROUP BY rd.n_vuelta
            ORDER BY rd.n_vuelta DESC LIMIT 1
        ";

        $currentDispatchRegister = \DB::select($query);

        if ($currentDispatchRegister) {
            $lastRoundTrip = $currentDispatchRegister[0]->last_round_trip;
            $roundTrips = range(1, $lastRoundTrip);
        } else {
            $roundTrips = [];
        }

        return view('partials.selects.roundTrips', compact('roundTrips'));
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
