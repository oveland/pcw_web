<?php

namespace App\Http\Controllers\Operation\Routes\Takings;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\Operation\FuelStation;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\RouteTaking;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Routes\RouteService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouteTakingsController extends Controller
{
    /**
     * @var RouteService
     */
    private $routeService;
    /**
     * @var PCWAuthService
     */
    private $auth;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $authService
     * @param RouteService $routeService
     */
    public function __construct(PCWAuthService $authService, RouteService $routeService)
    {
        $this->routeService = $routeService;
        $this->auth = $authService;
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @return Factory|View
     */
    public function form(DispatchRegister $dispatchRegister)
    {
        $company = $dispatchRegister->route->company;
        $fuelStations = $this->routeService->takings->getFuelStations($company);

        return view('operation.routes.takings.form', compact(['dispatchRegister', 'fuelStations']));
    }

    /**
     * @param Vehicle $vehicle
     * @param $date
     * @return Factory|Application|View
     */
    public function formCreate(Vehicle $vehicle, $date)
    {
        $dispatchRegister = DispatchRegister::where('date', $date)
            ->where('vehicle_id', $vehicle->id)
            ->where('status', 'takings')
            ->first();
        if (!$dispatchRegister) {
            $insert = \DB::select("INSERT INTO registrodespacho (fecha, hora, tipo_dia, id_empresa, n_turno, n_vuelta, n_vehiculo, n_placa, observaciones, cancelado, registradora_salida, registradora_llegada)
            VALUES ('$date', current_time, 'habil', $vehicle->company_id, 1, '1', '$vehicle->number', '$vehicle->plate', 'takings', TRUE, 0, 0 ) RETURNING id_registro");

            $id = collect($insert)->first()->id_registro;

            $dispatchRegister = DispatchRegister::find($id);
        }

        $lastRoute = DispatchRegister::completed()->where('date', '<', $date)->where('vehicle_id', $vehicle->id)->orderByDesc('id')->first()->route;

        $dispatchRegister->route()->associate($lastRoute);

        return view('operation.routes.takings.form', compact(['dispatchRegister']));
    }

    /**
     * @param Request $request
     * @param DispatchRegister $dispatchRegister
     * @return object
     */
    public function taking(Request $request, DispatchRegister $dispatchRegister)
    {
        return response()->json($this->routeService->takings->taking($dispatchRegister, $request->all()));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @return object
     */
    public function delete(DispatchRegister $dispatchRegister)
    {
        return response()->json($this->routeService->takings->deleteTakings($dispatchRegister));
    }

    /**
     * @param Request $request
     * @return Factory|View|string
     */
    public
    function ajax(Request $request)
    {
        switch ($request->get('option')) {
            case 'foo':
                return 'bar';
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
