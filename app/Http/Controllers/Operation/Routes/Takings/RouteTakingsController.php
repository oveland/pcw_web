<?php

namespace App\Http\Controllers\Operation\Routes\Takings;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\Operation\FuelStation;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\RouteTaking;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\Operation\Routes\Takings\RouteTakingsService;
use App\Services\Reports\Passengers\OccupationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouteTakingsController extends Controller
{
    /**
     * @var RouteTakingsService
     */
    private $service;
    /**
     * @var PCWAuthService
     */
    private $auth;
    /**
     * @var OccupationService
     */
    private $occupationService;

    /**
     * ReportRouteController constructor.
     * @param PCWAuthService $authService
     * @param RouteTakingsService $service
     */
    public function __construct(PCWAuthService $authService, RouteTakingsService $service, OccupationService $occupationService)
    {
        $this->service = $service;
        $this->auth = $authService;
        $this->occupationService = $occupationService;
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @return Factory|View
     */
    public function form(DispatchRegister $dispatchRegister)
    {
        $company = $dispatchRegister->route->company;
        $fuelStations = $this->service->getFuelStations($company);
        $occupationReport = $this->occupationService->getReportByDispatch($dispatchRegister);

        return view('operation.routes.takings.form', compact(['dispatchRegister', 'fuelStations', 'occupationReport']));
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
            $insert = \DB::select("INSERT INTO registrodespacho (fecha, hora, tipo_dia, id_empresa, n_turno, n_vuelta, n_vehiculo, n_placa, observaciones, cancelado, registradora_salida, registradora_llegada, ignore_trigger)
            VALUES ('$date', current_time, 'habil', $vehicle->company_id, 1, '1', '$vehicle->number', '$vehicle->plate', 'takings', TRUE, 0, 0, TRUE ) RETURNING id_registro");

            $id = collect($insert)->first()->id_registro;

            $dispatchRegister = DispatchRegister::find($id);
        }

        $lastDR = DispatchRegister::completed()->where('date', '<', $date)->where('vehicle_id', $vehicle->id)->orderByDesc('id')->first();
        $lastRoute = $lastDR ? $lastDR->route : $vehicle->company->activeRoutes->last();

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
        return response()->json($this->service->taking($dispatchRegister, $request->all()));
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @return object
     */
    public function delete(DispatchRegister $dispatchRegister)
    {
        return response()->json($this->service->deleteTakings($dispatchRegister));
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
