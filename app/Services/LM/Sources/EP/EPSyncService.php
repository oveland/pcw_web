<?php

namespace App\Services\LM\Sources\EP;

use App\Facades\EPDB;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use App\Services\LM\SyncService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Log;

class EPSyncService extends SyncService
{
    protected $type = 'dfs';

    function locations(Vehicle $vehicle, $date)
    {
    }

    function turns()
    {
    }

    function vehicles()
    {
    }

    function drivers()
    {
    }

    function trajectories()
    {
    }

    function marks()
    {
    }

    function routes()
    {
    }

    function tickets($date)
    {

        $dateFrom = Carbon::createFromFormat('Y-m-d', $date ?? Carbon::now()->toDateString())->toDateString();
        $dateTo = Carbon::createFromFormat('Y-m-d', $dateFrom)->addDays()->toDateString();

        $this->log("Start sync ticket passengers for date $dateFrom");

        $activeVehicles = $this->company->activeVehicles;
        $activeVehiclesQuery = $activeVehicles
            ->pluck('number')
            ->map(function ($number) {
                return "'$number'";
            })
            ->join(', ');

        $query = "
            SELECT 
                viaje travel_id, 
                FechaPartida date, 
                Codigo route_code, 
                Planilla spread_sheet, 
                Suben ascents, 
                Bajan descents, 
                bus vehicle_number,
                parada stop
            FROM v_saturacion_expal_h_III 
            WHERE FechaPartida between '$dateFrom' AND '$dateTo'
                AND bus IN ($activeVehiclesQuery) 
        ";

        $reportTicketsByVehicleNumber = EPDB::select($query)
            ->map(function ($report) {
                $report->vehicle_number = trim($report->vehicle_number);
                return $report;
            })
            ->groupBy('vehicle_number');

        $activeVehicles->each(function (Vehicle $vehicle) use ($reportTicketsByVehicleNumber, $dateFrom) {
            $report = $reportTicketsByVehicleNumber->get($vehicle->number);
            if ($report) $this->countsTicketsByVehicle($vehicle, $report, $dateFrom);
        });

        $this->log("End sync ticket passengers for date $dateFrom");
    }

    /**
     * @param Vehicle $vehicle
     * @param Collection $report
     * @return void
     */
    function countsTicketsByVehicle(Vehicle $vehicle, Collection $report, $date)
    {
        $drs = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId(
            $this->company,
            $date, null,
            null,
            $vehicle->id
        )
            ->active()
            ->orderBy('departure_time')
            ->get();

        $report->sortBy('date')->groupBy('travel_id')->each(function ($data) use ($drs) {
            $dataStops = collect([]);
            $dr = $drs->filter(function (DispatchRegister $dr) use ($data, $dataStops) {
                $data = collect($data);
                $dateStart = Carbon::createFromFormat('Y-m-d H:i:s.u', $data->first()->date);
                $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s.u', $data->last()->date);

                $data->sortBy('date')->each(function ($d) use ($dataStops) {
                    $dataStops->put($d->stop, [
                        'a' => $d->ascents,
                        'd' => $d->descents,
                    ]);
                });

//                if(!$dr->arrival_time_scheduled)dd($dr->id . " >> ". $dr->status);

                return $dr->arrival_time_scheduled && StrTime::isInclusiveTimeRanges(
                    $dateStart->toTimeString(),
                    $dateEnd->toTimeString(),
                    $dr->departure_time,
                    $dr->arrival_time_scheduled
                );
            })->first();

            if ($dr) {
                $passengers = intval(collect([$data->sum('ascents'), $data->sum('descents')])->average());
                $spreadSheet = $data->last()->spread_sheet;

                $drObs = $dr->getObservation('spreadsheet_passengers');
                $drObs->value = $passengers;
                $drObs->observation = $spreadSheet;
                $drObs->user_id = 2018101392; // Set user BOOTPCW
                $drObs->save();

                if ($dataStops) {
                    $drObsStops = $dr->getObservation('passengers_stops');
                    $drObsStops->value = 0;
                    $drObsStops->observation = $dataStops->toJson();
                    $drObsStops->user_id = 2018101392; // Set user BOOTPCW
                    $drObsStops->save();
                }
            }
        });
    }

    protected function log($message)
    {
        Log::info($this->company->short_name . " • $message");
    }
}