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
use DB;

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

    function debug($date)
    {
        $dateFrom = Carbon::createFromFormat('Y-m-d', $date ?? Carbon::now()->toDateString())->toDateString();
        $dateTo = '2023-11-17';

        $query = "
            SELECT bus, FechaPartida, HoraPartida, Codigo, viaje, Planilla, fecha_planilla, hora_planilla, TIPOVIAJE, parada, Origen, Destino, Suben, Bajan  
            FROM v_saturacion_expal_h_III 
            WHERE FechaPartida between '$dateFrom' AND '$dateTo'
                AND bus IN ('8419') order by FechaPartida
        ";

        EPDB::select($query)
            ->groupBy('Planilla')
            ->each(function ($rp, $planilla) {
                echo "\n • Agrupación por Planilla #$planilla \n";
                collect($rp)->each(function ($report, $planilla) {
                    echo trim($report->bus) . " • $report->FechaPartida $report->HoraPartida, $report->Codigo, $report->Origen - $report->Destino, • Viaje: $report->viaje, • Planilla: $report->Planilla, $report->fecha_planilla $report->hora_planilla, $report->Suben, $report->Bajan, $report->TIPOVIAJE, $report->parada";
                    echo "\n";
                });
            });


        return null;
    }

    function tickets($date)
    {
        //if ($date == '2023-11-11') return $this->debug($date);

        $dateFrom = Carbon::createFromFormat('Y-m-d', $date ?? Carbon::now()->toDateString())->toDateString();
        $dateTo = Carbon::createFromFormat('Y-m-d', $date)->addDays(1)->toDateString();

        $this->log("Start sync ticket passengers for date $dateFrom - $dateTo");

        $activeVehicles = $this->company->activeVehicles;
        $activeVehiclesQuery = $activeVehicles
            //->where('number', '8419')
            ->pluck('number')
            ->map(function ($number) {
                return "'$number'";
            })
            ->join(', ');

        $query = "
            SELECT 
                viaje           travel_id, 
                FechaPartida    date,
                Codigo          route_code, 
                Planilla        spread_sheet, 
                Suben           ascents, 
                Bajan           descents, 
                bus             vehicle_number,
                parada          stop,
                Origen          origin, 
                Destino         destiny
            FROM v_saturacion_expal_h_III 
            WHERE FechaPartida between '$dateFrom' AND '$dateTo 23:59:59'
                AND bus IN ($activeVehiclesQuery) 
        ";

        $reportTicketsByVehicleNumber = EPDB::select($query)
            ->map(function ($report) {
                $report->vehicle_number = trim($report->vehicle_number);
                return $report;
            })
            ->groupBy('vehicle_number');

        $activeVehicles->each(function (Vehicle $vehicle) use ($reportTicketsByVehicleNumber, $dateFrom, $dateTo) {
            $report = $reportTicketsByVehicleNumber->get($vehicle->number);
            if ($report) $this->countsTicketsByVehicle($vehicle, $report, $dateFrom, $dateTo);
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
    function countsTicketsByVehicle(Vehicle $vehicle, Collection $report, $date, $dateTo)
    {
        $drs = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId(
            $this->company,
            $date, $dateTo,
            null,
            $vehicle->id
        )
            ->active()
            ->get()
            ->sortBy(function (DispatchRegister $dr) {
                return "$dr->date $dr->departure_time";
            });
        $report->sortBy('date')->groupBy('travel_id')->each(function (Collection $data, $travelId) use ($drs, $vehicle) {
            echo " • FICS $vehicle->number Group by TravelID = $travelId";

            $dataStops = collect([]);
            $data = collect($data);
            $dateStart = Carbon::createFromFormat('Y-m-d H:i:s.u', $data->first()->date);
            $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s.u', $data->last()->date);

            $lastBySp = $data->groupBy('spread_sheet')->sort()->last()->last();
            echo " Planilla $lastBySp->spread_sheet • $lastBySp->origin - $lastBySp->destiny • date range group $dateStart to $dateEnd "." Asc: " . $data->sum('ascents') . " vs Desc: " . $data->sum('descents') . "\n";

            $data->each(function ($r) use ($vehicle) {
                echo "          > Viaje: $r->travel_id, $r->spread_sheet, $r->origin - $r->destiny, $r->date | Asc: $r->ascents vs Desc: $r->descents | Parada $r->stop\n";
            });

            $data->sortBy('date')->each(function ($d) use ($dataStops) {
                $dataStops->put($d->stop, [
                    'a' => $d->ascents,
                    'd' => $d->descents,
                ]);
            });


            $dr = $drs->filter(function (DispatchRegister &$dr) use ($dateStart, $dateEnd, $dataStops) {
                if (!$dr->date_end) {
                    $dateEndSchedule = collect(DB::select("SELECT ('$dr->date'::DATE + (SELECT get_route_total_time_from_dispatch_time(('$dr->date $dr->departure_time') :: TIMESTAMP, $dr->route_id))::INTERVAL)::DATE date_end"))->first()->date_end;
                    $dr->date_end = $dateEndSchedule;
                }

                $dr->date_end = Carbon::createFromFormat(strstr($dr->date_end, '/') ? 'd/m/Y' : 'Y-m-d', $dr->date_end)->toDateString();

                //echo "Compare " . "$dr->date $dr->departure_time to " . "$dr->date_end $dr->arrival_time_scheduled \n";

                return $dr->arrival_time_scheduled && StrTime::isInclusiveDateTimeRanges(
                        $dateStart->toDateTimeString(),
                        $dateEnd->toDateTimeString(),
                        "$dr->date $dr->departure_time",
                        "$dr->date_end $dr->arrival_time_scheduled"
                    );
            })->first();


            if ($dr) dump("    • DR Found: $dr->id • $dr->date $dr->departure_time to $dr->date_end $dr->arrival_time_scheduled . Asc: " . $data->sum('ascents') . " vs Desc: " . $data->sum('descents'));
            else echo "   x DR NOT found \n";

            if ($dr) {
                $dataBySpreadSheet = $data->groupBy('spread_sheet');
                if ($dataBySpreadSheet->count() > 1) $this->log(" ******** FICS duplicated Data for SS: " . $dataBySpreadSheet->keys()->join(', ') . " Dr associated: $dr->id");

                $data = $dataBySpreadSheet->sort()->last();
                $passengers = intval(collect([$data->sum('ascents'), $data->sum('descents')])->average());

                $spreadSheet = $data->last()->spread_sheet;

                $drObs = $dr->getObservation('spreadsheet_passengers_sync');
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
//        echo "$message\n";
        Log::info($this->company->short_name . " • $message");
    }
}