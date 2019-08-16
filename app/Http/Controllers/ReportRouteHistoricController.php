<?php

namespace App\Http\Controllers;

use App\Exports\HistoricRouteExport;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportRouteHistoricController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $pcwAuthService;
    /**
     * @var PCWExporterService
     */
    private $exporterService;

    public function __construct(PCWAuthService $pcwAuthService, PCWExporterService $exporterService)
    {
        $this->pcwAuthService = $pcwAuthService;
        $this->exporterService = $exporterService;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $access = $this->pcwAuthService->getAccessProperties();
        $companies = $access->companies;
        $routes = $access->routes;
        $vehicles = $access->vehicles;

        return view('reports.route.historic.index', compact(['companies', 'routes', 'vehicles']));
    }

    /**
     * @param Request $request
     * @return Factory|View|HistoricRouteExport
     * @throws Exception
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $vehicleReport = $request->get('vehicle-report');
        $forExport = $request->get('export');
        list($initialTime, $finalTime) = explode(';', $request->get('time-range-report'));

        $report = $this->buildHistoric($dateReport, $vehicleReport, $initialTime, $finalTime, $forExport);

        if ($forExport) return $this->exporterService->exportHistoricRoute($report);
        $report->exportLink = $request->getRequestUri() . "&export=true";

        return response()->json($report);
    }

    /**
     * @param $dateReport
     * @param $vehicleReport
     * @param $initialTime
     * @param $finalTime
     * @param bool $withAddress
     * @return object
     */
    public function buildHistoric($dateReport, $vehicleReport, $initialTime, $finalTime, $withAddress = false)
    {
        $vehicle = Vehicle::find($vehicleReport);

        $locations = Location::whereBetween('date', ["$dateReport $initialTime", "$dateReport $finalTime"])
            ->with('vehicle')
            ->with('dispatchRegister')
            ->with('vehicleStatus')
            ->where('vehicle_id', $vehicleReport)
            ->orderBy('date')
            ->get();

        $dataLocations = collect([]);

        foreach ($locations as $location) {
            $dispatchRegister = $location->dispatchRegister;
            $dataLocations->push((object)[
                'time' => $location->date->format('H:i:s'),
                'date' => $location->date->format('Y-m-d'),
                'currentMileage' => number_format(intval($location->current_mileage) / 1000, 2, '.', ''),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'address' => $location->getAddress($withAddress),
                'odometer' => $location->odometer,
                'orientation' => $location->orientation,
                'speed' => $location->speed,
                'speeding' => $location->speeding,
                'vehicleStatus' => (object)[
                    'status' => $location->vehicleStatus->des_status,
                    'iconClass' => $location->vehicleStatus->icon_class,
                    'mainClass' => $location->vehicleStatus->main_class,
                ],
                'dispatchRegister' => $dispatchRegister ? $dispatchRegister->getAPIFields() : null,
                'vehicle' => $location->vehicle->getAPIFields()
            ]);
        }

        $totalLocations = $dataLocations->count();

        $report = (object)[
            'dateReport' => $dateReport,
            'initialTime' => $initialTime,
            'finalTime' => $finalTime,
            'vehicle' => $vehicle->getAPIFields(),
            'historic' => $dataLocations,
            'total' => $totalLocations,
            'from' => $totalLocations ? $dataLocations->first()->time : '--:--',
            'to' => $totalLocations ? $dataLocations->last()->time : '--:--',
        ];

        return $report;
    }
}
