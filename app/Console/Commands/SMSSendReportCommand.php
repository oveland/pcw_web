<?php

namespace App\Console\Commands;

use App\CurrentLocationReport;
use App\Http\Controllers\API\SMS;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Log;

class SMSSendReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for send SMS locations reports';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$vehicleToReport = config('sms.sms_vehicle_report');
        $vehicleToReport = DB::select("SELECT val FROM params WHERE name='sms_vehicle_report' LIMIT 1")[0]->val ?? null;
        //$simToReport = config('sms.sms_vehicle_sim');
        $simToReport = DB::select("SELECT val FROM params WHERE name='sms_vehicle_sim' LIMIT 1")[0]->val ?? null;


        if( $simToReport && $simToReport ){
            $report = DB::select("
                SELECT v.plate vehicle_plate, v.number vehicle_number, r.name route_name, dr.round_trip round_trip, dr.turn, cr.date, cr.timed, cr.timep, cr.timem, dr.departure_time, (cr.timem::INTERVAL +dr.departure_time)::TIME time_m, (cr.timep::INTERVAL+dr.departure_time)::TIME time_p, 
                CASE WHEN ( abs(cr.status_in_minutes) <= 1 ) THEN 'ok' ELSE cr.status END status
                FROM current_reports cr
                  JOIN dispatch_registers dr ON (cr.dispatch_register_id = dr.id)
                  JOIN vehicles v ON (cr.vehicle_id = v.id)
                  JOIN routes r ON (dr.route_id = r.id)
                WHERE v.plate = '$vehicleToReport' AND (current_timestamp - cr.date)::INTERVAL < '00:00:40'::INTERVAL
            ");

            if( count($report) && $report = $report[0] ){
                Log::useDailyFiles(storage_path().'/logs/sms-report.log',10);
                $date = Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.',$report->date)[0])->toDateTimeString();

                $message = "$report->vehicle_plate ($report->vehicle_number):\nFecha: $date\n$report->route_name\nVuelta: $report->round_trip\nTurno: $report->turn\nDespachado: $report->departure_time\n\nProg.: $report->time_p\nMedido: $report->time_m\nEstado: $report->timed\n";

                $dataMessage = collect([
                    'vp' => $report->vehicle_plate,
                    'vn' => $report->vehicle_number,
                    'rd' => $date,
                    'rn' => $report->route_name,
                    'rr' => $report->round_trip,
                    'rt' => $report->turn,
                    'dpt' => $report->departure_time,
                    'sch' => $report->time_p,
                    'dif' => $report->timed,
                    'st' => $report->status
                ])->toJson();

                $dataMessage = str_replace('{','(',$dataMessage);
                $dataMessage = str_replace('}',')',$dataMessage);

                $sms = SMS::sendCommand($dataMessage, $simToReport);

                $log = "Send report for $vehicleToReport to $simToReport";
                Log::info($log);
                $this->info($log);

                $log = "Send: ".($sms["resultado"] === 0)?'Success':'Unsuccessfully';
                Log::info($log);
                $this->info($log);
            }
        }else{
            $this->info("Bad parametes configured :( -> simToReport $simToReport, simToReport: $simToReport");
        }
    }
}
