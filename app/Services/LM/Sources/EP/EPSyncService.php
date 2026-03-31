<?php

namespace App\Services\LM\Sources\EP;

use App\Facades\EPDB;
use App\Http\Controllers\Utils\StrTime;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
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
        //$dateTo = '2024-12-05';

        $this->log("Start sync ticket passengers for date $dateFrom - $dateTo");

        $activeVehicles = $this->company->activeVehicles;
        $activeVehiclesQuery = $activeVehicles
            //->where('number', '8511')
            ->pluck('number')
            ->map(function ($number) {
                return "'$number'";
            })
            ->join(', ');

        $query = "
            SELECT 
                vs.viaje         travel_id,
                FechaPartida    date,
                Codigo          route_code,
                Planilla        spread_sheet,
                Suben           ascents,
                Bajan           descents,
                bus             vehicle_number,
                parada          stop,
                Origen          origin,
                Destino         destiny,
                t.Nombre        driver_name,
                t.Documento     driver_document,
                t.Id            driver_code
            FROM v_saturacion_expal_h_III vs
            JOIN ViajesTripulantes vt on vt.Viaje = vs.viaje
            JOIN Tripulantes t on vt.Tripulante = t.Id
            WHERE FechaPartida between '$dateFrom' AND '$dateTo 23:59:59'
                AND bus IN ($activeVehiclesQuery)
        ";
        //dd($dateFrom);
        //dd(EPDB::select($query));
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

    function route($date){
        $dateFrom = Carbon::createFromFormat('Y-m-d', $date ?? Carbon::now()->toDateString())->toDateString();
        $dateTo = Carbon::createFromFormat('Y-m-d', $date)->addDays(1)->toDateString();

        $this->log("Start sync Routes for date $dateFrom - $dateTo");

        $activeVehicles = $this->company->activeVehicles;
        $activeVehiclesQuery = $activeVehicles
            ->where('number', '6011')// testing 6011
            ->pluck('number')
            ->map(function ($number) {
                return "'$number'";
            })
            ->join(', ');

        $query = "
            SELECT 
                Codigo          route_code
                Origen          origin,
                Destino         destiny
            FROM v_saturacion_expal_h_III vs
            WHERE FechaPartida between '$dateFrom' AND '$dateTo 23:59:59'
                AND bus IN ($activeVehiclesQuery)
        ";

        $reportTicketsByVehicleNumber = EPDB::select($query)
            ->map(function ($report) {
                return $report;
            })
            ->groupBy('vehicle_number');
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
        $report->sortBy('date')->groupBy('travel_id')->each(function (Collection $dataWithMultipleDrivers, $travelId) use ($drs, $vehicle) {
            echo " • FICS $vehicle->number Group by TravelID = $travelId";

            $dataStops = collect([]);
            $data = collect($dataWithMultipleDrivers)->groupBy('driver_code')->first();

            $dateStart = Carbon::createFromFormat('Y-m-d H:i:s.u', $data->first()->date);
            $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s.u', $data->last()->date);

            $lastBySp = $data->groupBy('spread_sheet')->sort()->last()->last();
            echo " Planilla $lastBySp->spread_sheet • $lastBySp->origin - $lastBySp->destiny • date range group $dateStart to $dateEnd " . " Asc: " . $data->sum('ascents') . " vs Desc: " . $data->sum('descents') . "\n";

            $data->each(function ($r) use ($vehicle) {
                echo "          > Viaje: $r->travel_id, $r->spread_sheet, $r->origin - $r->destiny, $r->date | Asc: $r->ascents vs Desc: $r->descents | Parada $r->stop\n";
            });

            $data->sortBy('date')->each(function ($d) use ($dataStops) {

                $dataStops->put($d->stop, [
                    'a' => $d->ascents,
                    'd' => $d->descents,
                    'time' => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s.u', $d->date)->format('H:i'),
                ]);
            });


            $dr = $drs->filter(function (DispatchRegister &$dr) use ($dateStart, $dateEnd, $dataStops) {
                if (!$dr->date_end) {
                    $dateEndSchedule = collect(DB::select("SELECT ('$dr->date'::DATE + (SELECT get_route_total_time_from_dispatch_time(('$dr->date $dr->departure_time') :: TIMESTAMP, $dr->route_id))::INTERVAL)::DATE date_end"))->first()->date_end;
                    $dr->date_end = $dateEndSchedule;
                }

                $dr->date_end = Carbon::createFromFormat(strstr($dr->date_end, '/') ? 'd/m/Y' : 'Y-m-d', $dr->date_end)->toDateString();
                if ($dr->arrival_time_scheduled) {
                    $dr->arrival_time_scheduled = strstr($dr->arrival_time_scheduled, '.') ?
                        Carbon::createFromFormat('H:i:s.u', $dr->arrival_time_scheduled)->format('H:i:s') :
                        Carbon::createFromFormat('H:i:s', $dr->arrival_time_scheduled)->format('H:i:s');
                }




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

                if ($vehicle->number == '8419') {
                    //dd($data->count(), $data);
                }

                $spreadSheet = $data->last()->spread_sheet;
                $routeFICS = $data->last()->origin . ' - ' . $data->last()->destiny;
                $driverCode = $data->last()->driver_code;
                $nameDriver = $data->last()->driver_name;
                $documentDriver = $data->last()->driver_document;
                $company = $this->company->id;
                $dateCreateDriver = Carbon::now()->toDateString();
                $drId = $dr->id;

                $drObs = $dr->getObservation('spreadsheet_passengers_sync');
                $drObs->value = $passengers;
                $drObs->observation = $spreadSheet !== null ? $spreadSheet : 0;
                $drObs->user_id = 2018101392; // Set user BOOTPCW
                $drObs->save();

                $drObs = $dr->getObservation('route_FICS');
                $drObs->value = 0;
                $drObs->observation =$routeFICS;
                $drObs->user_id = 2018101392; // Set user BOOTPCW
                $drObs->save();

                $drObs = $dr->getObservation('travel_id_fics');
                $drObs->value = $travelId;
                $drObs->observation = 'ID de viaje FICS';
                $drObs->user_id = 2018101392; // Set user BOOTPCW
                $drObs->save();



                // Sync passenger Origin / Destination details
                $queryPassengers = "
                    SELECT 
                         LocsOrigen.Nombre AS origin, 
                         LocsDestino.Nombre AS destiny,
                         COUNT(*) as total
                     FROM dbo.Pasajes p WITH (NOLOCK) 
                              INNER JOIN dbo.G_Localidades LocsOrigen WITH (NOLOCK) 
                                         ON p.LocalidadOrigen = LocsOrigen.LocalidadId 
                              INNER JOIN dbo.G_Localidades LocsDestino WITH (NOLOCK) 
                                         ON p.LocalidadDestino = LocsDestino.LocalidadId 
                     WHERE 
                         p.Viaje = $travelId
                     GROUP BY LocsOrigen.Nombre, LocsDestino.Nombre
                ";

                $passengerDetail = collect(EPDB::select($queryPassengers));

                if ($passengerDetail->isNotEmpty()) {
                    $drObs = $dr->getObservation('passenger_report_detail');
                    $drObs->value = $passengerDetail->sum('total');
                    $drObs->observation = $passengerDetail->toJson();
                    $drObs->user_id = 2018101392;
                    $drObs->save();
                }

                $this->processDrivers($dataWithMultipleDrivers, $dr);

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

    function processDrivers($dataWithMultipleDrivers, DispatchRegister $dr) {
        $company = $this->company->id;
        $count = 1;
        $dataWithMultipleDrivers->sortBy('driver_code')->groupBy('driver_code')->each(function($dataDriver, $driveCode) use ($company, $dr, &$count) {
            $driverCode = $dataDriver->last()->driver_code;
            $nameDriver = $dataDriver->last()->driver_name;
            $documentDriver = $dataDriver->last()->driver_document;
            $dateCreateDriver = Carbon::now()->toDateString();

            $driver = Driver::where('code', $driverCode)->first();
            if (!$driver) {
                $driverInsert = DB::select(
                    "INSERT INTO conductor (nombre1, apellido1, identidad, codigo_interno, empresa, creado)
                             VALUES (?, ' ', ?, ?, ?, ?) RETURNING id_idconductor",
                    [$nameDriver, $documentDriver, $driverCode, $company, $dateCreateDriver]
                );

                $driverId = collect($driverInsert)->first()->id_idconductor ?? 0;
            } else {
                $driverId = $driver->id;
            }

            DB::statement("UPDATE registrodespacho SET ignore_trigger = TRUE, driver_id = $driverId, codigo_interno_conductor = $driverCode WHERE id_registro = $dr->id");

            $drObs = $dr->getObservation('driver_id_' . $count);
            $drObs->value = $driverId;
            $drObs->observation = $driverCode;
            $drObs->user_id = 2018101392; // Set user BOOTPCW
            $drObs->save();

            $count++;
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