<?php

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
     * Get all events of a company and date
     *
     * @param Company $company
     * @param $initialDate
     * @param $finalDate
     * @param null $routeReport
     * @param null $vehicleReport
     * @return Location[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
    function all(Company $company, $initialDate, $finalDate, $routeReport = null, $vehicleReport = null)
    {
        $dispatchRegisters = DispatchRegister::completed()->whereCompanyAndDateRangeAndRouteIdAndVehicleId($company, $initialDate, $finalDate, $routeReport, $vehicleReport)->get();

        return Location::witOffRoads()
            ->forDate($initialDate, $finalDate)
            ->whereBetween('date', [$initialDate, $finalDate])
            ->whereIn('dispatch_register_id', $dispatchRegisters->pluck('id'))
            ->with(['vehicle', 'dispatchRegister', 'dispatchRegister.route', 'dispatchRegister.driver'])
            ->orderBy('date')
            ->get()->filter(function (Location $o) use ($initialDate, $finalDate) {
                $time = $o->date->toTimeString();
                $initialTime = collect(explode(' ', $initialDate))->get(1);
                $finalTime = collect(explode(' ', $finalDate))->get(1);

                return $time >= $initialTime && $time <= $finalTime;
            })->sortBy('id');
    }

    /**
     * Groups all events by vehicle
     *
     * @param $all
     * @return \Illuminate\Support\Collection
     */
    function groupByVehicles($all)
    {
        $eventsByVehicles = collect([]);
        foreach ($all->groupBy('vehicle_id') as $vehicleId => $allByVehicle) {
            $events = $this->groupByEvent($allByVehicle);
            if (count($events)) $eventsByVehicles->put($vehicleId, $events);
        }

        return $eventsByVehicles;
    }

    /**
     * Extract first event by route
     *
     * @param $allByVehicle
     * @return \Illuminate\Support\Collection
     */
    function groupByRoute($allByVehicle)
    {
        $events = $this->groupByEvent($allByVehicle);

        return $events
            ->sortBy(function ($event) {
                return $event->dispatchRegister->route->name;
            })
            ->groupBy(function ($event) {
                return $event->dispatchRegister->route->id;
            });
    }

    /**
     * Extract first event of the all locations
     *
     * @param Collection $allByVehicle
     * @param null $truncateTimeFromDispatchRegister
     * @return \Illuminate\Support\Collection
     */
    function groupByEvent(Collection $allByVehicle, $truncateTimeFromDispatchRegister = null)
    {
        $allByVehicle = $allByVehicle->where('off_road', true);
        $events = collect([]);
        if (!count($allByVehicle)) return $events;

        $includeAll = $allByVehicle->first()->vehicle->company_id == Company::ALAMEDA;

        $last = null;
        $displacement = collect([]);
        foreach ($allByVehicle as $event) {
            if ($last && Geolocation::getDistance($event->latitude, $event->longitude, $last->latitude, $last->longitude) > 10 || (!$last && $includeAll)) {
                $displacement->push($event);
            }
            $last = $event;
        }

        $last = null;
        $totalByGroup = 0;
        $firstEventOnGroup = null;

        $allByDispatchRegisters = $displacement->groupBy('dispatch_register_id');


        foreach ($allByDispatchRegisters as $allByDispatchRegister) {
            $dispatchRegister = $allByDispatchRegister->first()->dispatchRegister;

            if ($dispatchRegister && $dispatchRegister->hasValidOffRoad()) {
                if ($truncateTimeFromDispatchRegister || !$includeAll) {
                    $date = $dispatchRegister->getParsedDate()->toDateString();
                    $allByDispatchRegister = $allByDispatchRegister->where('date', '<=', "$date $dispatchRegister->arrival_time_scheduled");
                }

                // Detect first event
                foreach ($allByDispatchRegister as $event) {
                    if (!$last || $event->date->diff($last->date)->format('%H:%I:%S') > '00:03:00') {
                        $firstEventOnGroup = $event;
                        $totalByGroup = 1;
                    } else if ($totalByGroup > 0) {
                        $totalByGroup++;
                    }

                    if ($totalByGroup > 3 || ($includeAll && $totalByGroup >= 1)) {
                        if ($firstEventOnGroup->isTrueOffRoad()) {
                            $events->push($firstEventOnGroup);
                        }
                        $totalByGroup = 0;
                    }

                    $last = $event;
                }
            }
        }

        return $events;
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