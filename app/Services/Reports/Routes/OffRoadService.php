<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 10/10/2018
 * Time: 10:16 PM
 */

namespace App\Services\Reports\Routes;


use App\Http\Controllers\Utils\Geolocation;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use App\Services\PCWExporterService;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class OffRoadService
{
    /**
     * Generate detailed off road report for all vehicles of a company in a date
     *
     * @param Company $company
     * @param $initialDate
     * @param $finalDate
     * @return \Illuminate\Support\Collection
     */
    function offRoadByVehiclesReport(Company $company, $initialDate, $finalDate)
    {
        $offRoadByVehiclesReport = collect([]);
        $offRoadsByVehiclesByRoutes = self::groupByFirstOffRoadByRoute($this->allOffRoads($company, $initialDate, $finalDate));

        foreach ($offRoadsByVehiclesByRoutes as $vehicleId => $offRoadsByRoutes) {
            $offRoadByVehiclesReport->put($vehicleId, [
                'vehicle' => Vehicle::find($vehicleId),
                'offRoadsByRoutes' => $offRoadsByRoutes,
                'totalOffRoads' => $offRoadsByRoutes->sum(function ($route) {
                    return count($route);
                })
            ]);
        }

        return $offRoadByVehiclesReport;
    }

    /**
     * Get all offRoads of a company and date
     *
     * @param Company $company
     * @param $initialDate
     * @param $finalDate
     * @param null $routeReport
     * @param null $vehicleReport
     * @return Location[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
    function allOffRoads(Company $company, $initialDate, $finalDate, $routeReport = null, $vehicleReport = null)
    {
        /*
            $allSpeeding = Location::whereBetween('date', [$initialDate, $finalDate])->witOffRoads();

            if($routeReport == 'all' || !$routeReport){
                $vehicles = $company->vehicles();
                if($vehicleReport != 'all'){
                    $vehicles = $vehicles->where('id', $vehicleReport);
                }

                $allSpeeding = $allSpeeding->whereIn('vehicle_id', $vehicles->get()->pluck('id'));
            }else{
                $dispatchRegisters = DispatchRegister::completed()->whereCompanyAndDateAndRouteIdAndVehicleId($company, $initialDate, $routeReport, $vehicleReport)->get();
                $allSpeeding = $allSpeeding->whereIn('dispatch_register_id', $dispatchRegisters->pluck('id'));
            }

            return $allSpeeding->orderBy('date')->get();
         */

        $dispatchRegisters = DispatchRegister::completed()->whereCompanyAndDateRangeAndRouteIdAndVehicleId($company, $initialDate, $finalDate, $routeReport, $vehicleReport)->get();
        $allOffRoads = Location::witOffRoads();


        if (explode(' ', $initialDate)[0] == explode(' ', $finalDate)[0]) {
            $allOffRoads = $allOffRoads->forDate($initialDate);
        }


        $allOffRoads = $allOffRoads->whereBetween('date', [$initialDate, $finalDate])
            ->whereIn('dispatch_register_id', $dispatchRegisters->pluck('id'))
            ->with(['vehicle', 'dispatchRegister', 'dispatchRegister.route', 'dispatchRegister.driver'])
            ->orderBy('date');

        $allOffRoads = $allOffRoads->get();

        $allOffRoads = $allOffRoads
            ->filter(function (Location $o) use ($initialDate, $finalDate) {
                $time = $o->date->toTimeString();
                $initialTime = collect(explode(' ', $initialDate))->get(1);
                $finalTime = collect(explode(' ', $finalDate))->get(1);

                return $time >= $initialTime && $time <= $finalTime;
            })->sortBy('id');

        return $allOffRoads;
    }

    /**
     * Groups all off roads by vehicle and first event
     *
     * @param $allOffRoads
     * @return \Illuminate\Support\Collection
     */
    function offRoadsByVehicles($allOffRoads)
    {
        $allOffRoadsByVehicles = $allOffRoads->groupBy('vehicle_id');

        $offRoadsByVehicles = collect([]);
        foreach ($allOffRoadsByVehicles as $vehicleId => $offRoadsByVehicle) {
            $offRoadsEvents = self::groupByFirstOffRoadEvent($offRoadsByVehicle);
            if (count($offRoadsEvents)) $offRoadsByVehicles->put($vehicleId, $offRoadsEvents);
        }

        return $offRoadsByVehicles;
    }

    /**
     * Get all offRoads of a company and date
     *
     * @param Vehicle $vehicle
     * @param $dateReport
     * @return Location[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
    function offRoadsByVehicle(Vehicle $vehicle, $dateReport)
    {
        return Location::forDate($dateReport)->whereBetween('date', [$dateReport . ' 00:00:00', $dateReport . ' 23:59:59'])
            ->witOffRoads()
            ->where('vehicle_id', $vehicle->id)
            ->orderBy('date')
            ->get();
    }

    /**
     * Get all offRoads of a dispatch register
     *
     * @param DispatchRegister $dispatchRegister
     * @return Location[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
    function byDispatchRegister(DispatchRegister $dispatchRegister)
    {
        $allOffRoadsByDispatchRegister = Location::forDate($dispatchRegister->getParsedDate()->toDateString())->witOffRoads()
            ->where('dispatch_register_id', $dispatchRegister->id)
            ->orderBy('date')
            ->get();

        return self::groupByFirstOffRoadEvent($allOffRoadsByDispatchRegister);
    }

    /**
     * Extract first event of the all off roads and group it by route
     *
     * @param $offRoadsByVehicle
     * @return \Illuminate\Support\Collection
     */
    static function groupByFirstOffRoadByRoute($offRoadsByVehicle)
    {
        $offRoadsEvents = self::groupByFirstOffRoadEvent($offRoadsByVehicle);

        $offRoadsEventsByRoutes = $offRoadsEvents
            ->sortBy(function ($offRoad, $key) {
                return $offRoad->dispatchRegister->route->name;
            })
            ->groupBy(function ($offRoad, $key) {
                return $offRoad->dispatchRegister->route->id;
            });

        return $offRoadsEventsByRoutes;
    }

    /**
     * @param $offRoadsByVehicle
     * @return \Illuminate\Support\Collection
     */
    function groupByFirstOffRoad($offRoadsByVehicle)
    {
        return self::groupByFirstOffRoadEvent($offRoadsByVehicle);
    }

    /**
     * Extract first event of the all off roads
     *
     * @param Collection $offRoadsByVehicle
     * @param null $truncateTimeFromDispatchRegister
     * @return \Illuminate\Support\Collection
     */
    static function groupByFirstOffRoadEvent($offRoadsByVehicle, $truncateTimeFromDispatchRegister = null)
    {
        $offRoadsByVehicle = $offRoadsByVehicle->where('off_road', true);
        $offRoadsEvents = collect([]);
        if (!count($offRoadsByVehicle)) return $offRoadsEvents;

        $includeAll = $offRoadsByVehicle->first()->vehicle->company_id == Company::ALAMEDA;

        // Filter locations with signification movement
        $lastOffRoad = null;
        $offRoadMove = collect([]);
        foreach ($offRoadsByVehicle as $offRoad) {
            if ($lastOffRoad && Geolocation::getDistance($offRoad->latitude, $offRoad->longitude, $lastOffRoad->latitude, $lastOffRoad->longitude) > 10 || (!$lastOffRoad && $includeAll)) {
                $offRoadMove->push($offRoad);
            }
            $lastOffRoad = $offRoad;
        }

        $lastOffRoad = null;
        $totalByGroup = 0;
        $firstOffRoadOnGroup = null;

        $offRoadByDispatchRegisters = $offRoadMove->groupBy('dispatch_register_id');


        foreach ($offRoadByDispatchRegisters as $offRoadByDispatchRegister) {
            $dispatchRegister = $offRoadByDispatchRegister->first()->dispatchRegister;

            if ($dispatchRegister && $dispatchRegister->hasValidOffRoad()) {
                if ($truncateTimeFromDispatchRegister || !$includeAll) {
                    $date = $dispatchRegister->getParsedDate()->toDateString();
                    $offRoadByDispatchRegister = $offRoadByDispatchRegister->where('date', '<=', "$date $dispatchRegister->arrival_time_scheduled");
                }

                // Detect off road event as first location with off road in more than 5 minutes
                foreach ($offRoadByDispatchRegister as $offRoad) {

//                dump($offRoad->vehicle->number);

                    if (!$lastOffRoad || $offRoad->date->diff($lastOffRoad->date)->format('%H:%I:%S') > '00:03:00') {
                        $firstOffRoadOnGroup = $offRoad;
                        $totalByGroup = 1;
                    } else if ($totalByGroup > 0) {
                        $totalByGroup++;
                    }

                    if ($totalByGroup > 3 || ($includeAll && $totalByGroup >= 1)) {
                        if ($firstOffRoadOnGroup->isTrueOffRoad()) {
                            $offRoadsEvents->push($firstOffRoadOnGroup);
                        }
                        $totalByGroup = 0;
                    }

                    $lastOffRoad = $offRoad;
                }
            }
        }

        return $offRoadsEvents;
    }

    /**
     * @param $dataReport
     * @param $query
     * @return LaravelExcelWriter
     */
    public function exportByVehicles($dataReport, $query)
    {
        return Excel::create(__('Off Roads') . " $query->dateReport", function ($excel) use ($dataReport, $query) {
            foreach ($dataReport as $vehicleId => $reports) {
                $vehicle = Vehicle::find($vehicleId);
                $dataExcel = array();

                foreach ($reports as $report) {
                    $dispatchRegister = $report->dispatchRegister;
                    $route = $dispatchRegister->route;

                    $link = route('link-report-route-chart-view', ['dispatchRegister' => $dispatchRegister->id, 'location' => $report->id]);

                    $dataExcel[] = [
                        __('N°') => count($dataExcel) + 1,                                                              # A CELL
                        __('Date') => $report->date->toDateString(),                                                # G CELL
                        __('Time') => $report->date->toTimeString(),                                                # G CELL
                        __('Vehicle') => intval($vehicle->number),                                                      # B CELL
                        __('Route') => $route->name,                                                                    # D CELL
                        __('Turn') => $dispatchRegister->turn,                                                          # E CELL
                        __('Round Trip') => $dispatchRegister->round_trip,                                              # F CELL
                        //__('Location') => Geolocation::getAddressFromCoordinates($report->latitude, $report->longitude),
                        __('Details') => $link                                                                          # H CELL
                    ];
                }

                $dataExport = (object)[
                    'fileName' => __('Off road report by Vehicle') . " $query->dateReport",
                    'title' => __('Off road report by Vehicle') . " $query->dateReport",
                    'subTitle' => "$vehicle->number",
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'offRoadReport'
                ];
                $excel = PCWExporterService::createHeaders($excel, $dataExport);
                $excel = PCWExporterService::createSheet($excel, $dataExport);
            }
        })->export('xlsx');
    }
}