<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 11/01/2020
 * Time: 10:59 AM
 */

namespace App\Services\Reports\Vehicles;


use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Reports\ConsolidatedRouteVehicle;
use App\Models\Vehicles\Vehicle;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;

use App\Services\PCWTime;
use App\Services\Reports\Routes\OffRoadService;
use App\Services\Reports\Routes\SpeedingService;
use App\Traits\CounterByRecorder;
use App\Traits\CounterBySensor;

use App\Services\PCWExporterService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Excel;

class ConsolidatedService
{
    /**
     * @var OffRoadService
     */
    private $offRoadService;
    /**
     * @var SpeedingService
     */
    private $speedingService;

    public function __construct(OffRoadService $offRoadService, SpeedingService $speedingService)
    {
        $this->offRoadService = $offRoadService;
        $this->speedingService = $speedingService;
    }

    public function build(Company $company, Carbon $initialDate, Carbon $finalDate)
    {
        Log::useDailyFiles(storage_path() . '/logs/consolidatedVehicle.log', 10);
        $dateRange = collect(PCWTime::dateRange($initialDate, $finalDate, false, true))->toArray();

        foreach ($dateRange as $date) {
            $dispatchRegisters = DispatchRegister::completed()
                ->with(['locations', 'route', 'vehicle'])
                ->whereIn('vehicle_id', $company->vehicles->pluck('id'))
                ->where('date', $date)
                //->whereIn('id', [738473, 737877, 739513])
                ->get();

            $dispatchRegistersByRoutes = $dispatchRegisters
                ->groupBy('route_id');
            $totalRoutes = $dispatchRegistersByRoutes->count();
            Log::info("CONSOLIDATED $date >> " . $dispatchRegisters->count() . " dispatch registers and $totalRoutes routes");
            $indexRoute = 1;
            foreach ($dispatchRegistersByRoutes as $routeId => $dispatchRegistersByRoute) {
                $route = Route::find($routeId);

                $dispatchRegistersByRouteAndVehicles = $dispatchRegistersByRoute->groupBy('vehicle_id');
                $percent = intval((100 / $totalRoutes) * $indexRoute);
                $indexRoute++;
                Log::info("     Process $date ($percent%) $route->name >> " . $dispatchRegistersByRouteAndVehicles->count() . " vehicles");

                foreach ($dispatchRegistersByRouteAndVehicles as $vehicleId => $turns) {
                    $vehicle = Vehicle::find($vehicleId);

                    $allRouteLocations = collect([]);
                    foreach ($turns as $turn) {
                        $locations = $turn->locations;
                        foreach ($locations as $location) {
                            $allRouteLocations->push($location);
                        }
                    }

                    //Log::info("         - Vehicle $vehicle->number >> ".$allRouteLocations->count()." locations");

                    $offRoadEventLocation = $this->offRoadService->groupByFirstOffRoad($allRouteLocations);

                    $speedingEventLocation = $this->speedingService->groupByFirstSpeedingEvent($allRouteLocations);

                    $consolidated = ConsolidatedRouteVehicle::where('date', $date)
                        ->where('route_id', $route->id)
                        ->where('vehicle_id', $vehicle->id)
                        ->first();
                    if (!$consolidated) $consolidated = new ConsolidatedRouteVehicle();

                    $consolidated->fill([
                        'date' => $date,
                        'total_off_roads' => $offRoadEventLocation->count(),
                        'total_speeding' => $speedingEventLocation->count(),
                        'total_locations' => $allRouteLocations->count(),
                    ]);

                    $consolidated->vehicle()->associate($vehicle);
                    $consolidated->route()->associate($route);

                    $consolidated->save();
                }
            }
        }

        Log::info("    ***** CONSOLIDATED finished!!!");
    }

    /**
     * @param Company $company
     * @param Carbon $initialDate
     * @param Carbon $finalDate
     */
    public function export(Company $company, Carbon $initialDate, Carbon $finalDate)
    {
        $initialDate = $initialDate->toDateString();
        $finalDate = $finalDate->toDateString();
        $vehicles = $company->vehicles;

        $consolidated = ConsolidatedRouteVehicle::where('date', '>=', $initialDate)
            ->where('date', '<=', $finalDate)
            ->whereIn('vehicle_id', $vehicles->pluck('id'))
            ->get();


        $consolidatedByRoutes = $consolidated->groupBy('route_id');

        Excel::create(__('Consolidated route vehicle'), function ($excel) use ($consolidatedByRoutes, $initialDate, $finalDate) {
            foreach ($consolidatedByRoutes as $routeId => $consolidatedByRoute) {
                $dataExcel = array();
                $route = Route::find($routeId);
                foreach ($consolidatedByRoute->sortBy('date') as $consolidated) {
                    $vehicle = $consolidated->vehicle;
                    $date = $consolidated->date->toDateString();
                    $totalOffRoads = $consolidated->total_off_roads;
                    $totalSpeeding = $consolidated->total_speeding;

                    $dataExcel[] = [
                        __('Date') => $date,                                                                # A CELL
                        __('Route') => $route->name,                                                        # B CELL
                        __('Vehicle') => $vehicle->number,                                                  # C CELL
                        __('Plate') => $vehicle->plate,                                                     # D CELL
                        __('Off Roads') => $totalOffRoads,                                                  # E CELL
                        __('Speeding') => $totalSpeeding,                                                   # F CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => __('Consolidated route vehicle'),
                    'title' => __('Consolidated route vehicle'),
                    'subTitle' => "$initialDate - $finalDate",
                    'sheetTitle' => "$route->name",
                    'data' => $dataExcel,
                    'type' => 'consolidatedRouteVehicle'
                ];
                /* SHEETS */
                $excel = PCWExporterService::createHeaders($excel, $dataExport);
                $excel = PCWExporterService::createSheet($excel, $dataExport);
            }
        })->export('xlsx');
    }
}