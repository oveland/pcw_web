<?php

namespace App\Http\Controllers;

use App\Exports\HistoricRouteExport;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use App\Services\Auth\PCWAuthService;
use App\Services\PCWExporterService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Auth;
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
            ->where('vehicle_id', $vehicleReport)
            ->with(['vehicle', 'dispatchRegister', 'vehicleStatus'])
            ->orderBy('date')
            ->get();


        $dataLocations = collect([]);

        $lastLocation = $locations->first();

        foreach ($locations as $location) {
            $dispatchRegister = $location->dispatchRegister;

            $period = '';
            $averagePeriod = '';
            if (Auth::user()->isAdmin()) {
                $period = $location->date->diffInSeconds($lastLocation->date);
                //$averagePeriod = intval($dataLocations->average('period')); // CAUTION this line take some long time!
                $averagePeriod = "--";
            }

            $dataLocations->push((object)[
                'time' => $location->date->format('H:i:s'),
                'period' => $period,
                'averagePeriod' => $averagePeriod,
                'date' => $location->date->format('Y-m-d'),
                'currentMileage' => number_format(intval($location->current_mileage) / 1000, 2, '.', ''),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'address' => $location->getAddress($withAddress),
                'odometer' => $location->odometer,
                'orientation' => $location->orientation,
                'speed' => $location->speed,
                'speeding' => $location->speeding,
                'offRoad' => $location->off_road,
                'routeDistance' => number_format(intval($location->distance) / 1000, 2, '.', ''),
                'vehicleStatus' => (object)[
                    'status' => $location->vehicleStatus->des_status,
                    'iconClass' => $location->vehicleStatus->icon_class,
                    'mainClass' => $location->vehicleStatus->main_class,
                ],
                'dispatchRegister' => $dispatchRegister ? $dispatchRegister->getAPIFields() : null,
                'vehicle' => $location->vehicle->getAPIFields()
            ]);
            $lastLocation = $location;
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
