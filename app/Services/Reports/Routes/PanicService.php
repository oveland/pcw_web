<?php

namespace App\Services\Reports\Routes;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleStatus;
use App\Services\PCWExporterService;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class PanicService
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
        $vehicles = $company->activeVehicles();

        if ($vehicleReport) {
            if (is_numeric($vehicleReport)) {
                $vehicles = $vehicles->where('id', $vehicleReport);
            } else {
                $vehicles = $vehicles->where('tags', 'like', "%$vehicleReport%");
            }
        }

        return Location::withPanic()
            ->forDate($initialDate, $finalDate)
            ->whereBetween('date', [$initialDate, $finalDate])
            ->whereIn('vehicle_id', $vehicles->get()->pluck('id'))
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
     * Extract first event of the all registers
     *
     * @param $allByVehicle
     * @return \Illuminate\Support\Collection
     */
    public function groupByEvent($allByVehicle)
    {
        $allByVehicle = $allByVehicle->where('vehicle_status_id', VehicleStatus::PANIC);

        $events = collect([]);
        if (!count($allByVehicle)) return $events;

        $last = null;
        foreach ($allByVehicle as $event) {
            if ($last) {
                if ($event->time->diff($last->time)->format('%H:%I:%S') > '00:05:00') {
                    $events->push($event);
                }
            } else {
                $events->push($event);
            }
            $last = $event;
        }

        return $events->sortBy('date');
    }

    /**
     * @param $dataReport
     * @param $query
     * @return LaravelExcelWriter
     */
    public function exportByVehicles($dataReport, $query)
    {
        return Excel::create(__('Panics') . " $query->dateReport", function ($excel) use ($dataReport, $query) {
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
                    'fileName' => __('Panic report by Vehicle') . " $query->dateReport",
                    'title' => __('Panic report by Vehicle') . " $query->dateReport",
                    'subTitle' => "$vehicle->number",
                    'sheetTitle' => "$vehicle->number",
                    'data' => $dataExcel,
                    'type' => 'panicReport'
                ];
                $excel = PCWExporterService::createHeaders($excel, $dataExport);
                $excel = PCWExporterService::createSheet($excel, $dataExport);
            }
        })->export('xlsx');
    }
}