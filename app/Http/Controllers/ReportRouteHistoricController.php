<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Models\Vehicles\Location;
use Auth;
use Illuminate\Http\Request;

class ReportRouteHistoricController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->get();
        }
        return view('reports.route.historic.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $companyReport = $request->get('company-report');
        $dateReport = $request->get('date-report');
        $vehicleReport = $request->get('vehicle-report');
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $locations = Location::whereBetween('date',["$dateReport $initialTime", "$dateReport $finalTime"])
            ->with('vehicle')
            ->with('dispatchRegister')
            ->with('vehicleStatus')
            ->where('vehicle_id', $vehicleReport)
            ->orderBy('id')
        ->get();

        $historic = $this->buildHistoric($locations);

        return response()->json($historic);
    }

    /**
     * @param $locations
     * @return \Illuminate\Support\Collection
     */
    public function buildHistoric($locations)
    {
        $dataLocations = collect([]);

        foreach ($locations as $location){
            $dataLocations->push((object)[
                'time' => $location->date->format('H:i:s'),
                'currentMileage' => number_format(intval($location->current_mileage)/1000, 2, '.', ''),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'odometer' => $location->odometer,
                'orientation' => $location->orientation,
                'speed' => $location->speed,
                'speeding' => $location->speeding,
                'vehicleStatus' => [
                    'status' => $location->vehicleStatus->des_status,
                    'iconClass' => $location->vehicleStatus->icon_class,
                    'mainClass' => $location->vehicleStatus->main_class,
                ],
                'dispatchRegister' => $location->dispatchRegister ? $location->dispatchRegister->getAPIFields() : null,
                'vehicle' => $location->vehicle->getAPIFields()
            ]);
        }

        $totalLocations = $dataLocations->count();

        $report = collect([
            'historic' => $dataLocations,
            'total' => $totalLocations,
            'from' => $totalLocations ? $dataLocations->first()->time : '--:--',
            'to' => $totalLocations ? $dataLocations->last()->time : '--:--',
        ]);

        return $report;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     * @throws \GuzzleHttp\Exception\GuzzleException
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
