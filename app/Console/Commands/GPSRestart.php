<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\SMS;
use App\Vehicle;
use DB;
use Illuminate\Console\Command;

class GPSRestart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart GPS down via sms';

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
        $backDaysForSendSms = config('sms.back_days_for_send_sms');
        $backTimeForSendSMS = config('sms.back_time_for_send_sms');

        $query = "
            SELECT
              m.id id,
              (m.fecha||' '||m.hora)::TIMESTAMP date,
              m.lat latitude,
              m.lng longitude,
              m.orientacion orientation,
              m.velocidad speed,
              m.status status,
              sv.des_status status_name,
              v.id vehicle_id,
              m.name vehicle_plate
            FROM markers AS m
              JOIN vehicles AS v ON (v.plate = m.name)
              JOIN status_vehi AS sv ON (sv.id_status = m.status)
            WHERE
              m.fecha > current_Date - $backDaysForSendSms AND (m.fecha||' '||m.hora)::TIMESTAMP < (current_timestamp - '$backTimeForSendSMS'::INTERVAL ) AND status = 1;
        ";

        $downGPSList = DB::select($query);

        if(count($downGPSList)){
            foreach ($downGPSList as $downGPS) {
                $vehicle = Vehicle::find($downGPS->vehicle_id);
                $response = SMS::sendResetCommandToVehicle($vehicle);
                $this->info($response->log);
            }
        }
        else{
            $this->info("Hey there are not down gps");
            $this->info($query);
        }
    }
}
