<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PassengerReportByFringesController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('reports.passengers.recorders.fringes.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function search(Request $request)
    {
        $companyReport = $request->get('company-report');
        $dateReport = $request->get('date-report');
        $routeReport = $request->get('route-report');

        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;

        $dispatchRegisters = DispatchRegister::where('date', $dateReport)->active();
        if ($routeReport == 'all') {
            dd(__('Select a route'));
            $dispatchRegisters = $dispatchRegisters->whereIn('route_id', $company->routes->pluck('id'));
        } else $dispatchRegisters = $dispatchRegisters->where('route_id', $routeReport);
        $dispatchRegisters = $dispatchRegisters->orderBy('departure_time')->get();

        $dispatchRegistersByVehicles = $dispatchRegisters->sortBy(function ($dispatchRegister, $routeId) {
            return $dispatchRegister->vehicle->number;
        })->groupBy('vehicle_id');

        switch ($request->get('group-by')) {
            case 'fringes':
                $route = Route::find($routeReport);
                $fringes = $route->fringes($dispatchRegisters->first()->type_of_day ?? 0);
                return view('reports.passengers.recorders.fringes.fringes', compact(['dispatchRegistersByVehicles', 'fringes']));
                break;
            case 'fringes-merged':
                $route = Route::find($routeReport);
                $fringes = $route->fringes($dispatchRegisters->first()->type_of_day ?? 0);
                return view('reports.passengers.recorders.fringes.fringesMerged', compact(['dispatchRegistersByVehicles', 'fringes']));
                break;
            case 'round_trips':
                return view('reports.passengers.recorders.fringes.roundTrips', compact('dispatchRegistersByVehicles'));
                break;
            default:
                return null;
                break;
        }
    }
}
