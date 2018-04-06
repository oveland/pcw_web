<?php

namespace App\Console\Commands;

use App\CurrentLocationReport;
use App\Http\Controllers\API\SMS;
use DB;
use Illuminate\Console\Command;

class SMSSendReport extends Command
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
        $vehicleToReport = env('SMS_VEHICLE_REPORT');
        $simToReport = env('SMS_VEHICLE_SIM');

        if( $simToReport && $simToReport ){
            $report = DB::select("
            SELECT v.plate vehicle_plate, v.number vehicle_number, r.name route_name, dr.round_trip round_trip, dr.turn, cr.date, cr.timed, cr.timep, cr.timem, dr.departure_time, (cr.timem::INTERVAL +dr.departure_time)::TIME time_m, (cr.timep::INTERVAL+dr.departure_time)::TIME time_p
            FROM current_reports cr
              JOIN dispatch_registers dr ON (cr.dispatch_register_id = dr.id)
              JOIN vehicles v ON (cr.vehicle_id = v.id)
              JOIN routes r ON (dr.route_id = r.id)
            WHERE v.plate = '$vehicleToReport'
        ");

            if( count($report) && $report = $report[0] ){
                dump("Send report for $vehicleToReport to $simToReport");
                $date = explode('.',$report->date)[0];
                $message = "$report->vehicle_plate ($report->vehicle_number):\nFecha: $date\n$report->route_name\nVuelta: $report->round_trip\nTurno: $report->turn\nDespachado: $report->departure_time\n\nProg.: $report->time_p\nMedido: $report->time_m\nEstado: $report->timed\n
            ";

                $sms = SMS::sendCommand($message, $simToReport);
                dump("Send: ".($sms["resultado"] === 0)?'Success':'Unsuccessfully');
            }
        }else{
            dump("Bad parametes configured :( -> simToReport $simToReport, simToReport: $simToReport");
        }
    }
}
