<?php

namespace App\Services\LM\Sources\EP;

use App\Facades\EPDB;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\LM\Sync;
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

    function newTickets()
    {
        $this->log("* Start sync for new passengers tickets...");

        $activeVehicles = $this->company->activeVehicles;
        $activeVehiclesQuery = $activeVehicles
            ->pluck('number')
            ->map(function ($number) {
                return "'$number'";
            })
            ->join(', ');

        $lastID = $this->getLastSync()->last_id;

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
            WHERE id > $lastID AND bus IN ($activeVehiclesQuery) 
        ";

        $reportTickets = collect(EPDB::select($query))
            ->map(function ($report) {
                $report->vehicle_number = trim($report->vehicle_number);
                return $report;
            });

        $FICSDates = $reportTickets->groupBy(function ($r) {
            return Carbon::createFromFormat('Y-m-d H:i:s.u', $r->date)->toDateString();
        })->keys()->join(', ');

        $this->log("*           . Sync FICS for dates: " . $FICSDates);

        $reportTicketsByVehicleNumber = $reportTickets->groupBy('vehicle_number');

        $activeVehicles->each(function (Vehicle $vehicle) use ($reportTicketsByVehicleNumber) {
            $report = $reportTicketsByVehicleNumber->get($vehicle->number);

            if ($report) {
                collect($report)
                    ->groupBy(function ($r) {
                        return Carbon::createFromFormat('Y-m-d H:i:s.u', $r->date)->toDateString();
                    })
                    ->each(function ($r, $date) use ($vehicle) {
                        $this->countsTicketsByVehicle($vehicle, $r, $date);
                    });
            }
        });

        $this->log("* End sync for new passengers tickets");
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

        $report->sortBy('date')->groupBy('travel_id')->each(function (Collection $data) use ($drs) {
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
                $dataBySpreadSheet = $data->groupBy('spread_sheet');
                if ($dataBySpreadSheet->count() > 1) $this->log(" ******** FICS duplicated Data for SS: " . $dataBySpreadSheet->keys()->join(', ') . " Dr associated: $dr->id");

                $data = $dataBySpreadSheet->sort()->last();
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

    function getLastSync(Company $company)
    {
        $lmSync = $company->lmSync()->get();

        if (!$lmSync) {
            $lmSync = new Sync();
            $lmSync->company()->associate($company);
            $lmSync->last_id = 0;
            $lmSync->last_date = Carbon::now()->subDays(30);
            $lmSync->save();
        }
    }

    protected function log($message)
    {
        Log::info($this->company->short_name . " • $message");
    }
}