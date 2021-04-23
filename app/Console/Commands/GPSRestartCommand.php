<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\SMS;
use App\Models\Vehicles\Vehicle;
use DB;
use Illuminate\Console\Command;

class GPSRestartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:restart {--company=all}';

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
        $company = $this->option('company');
        $companyQuery = $company == 'all'?'':" AND v.company_id = $company";

        $query = "
            SELECT
              m.id id,
              (m.fecha||' '||m.hora)::TIMESTAMP date,
              sv.des_status status_name,
              v.id vehicle_id,
              v.number vehicle,
              v.active,
              v.in_repair,
              c.short_name company,
              m.lat latitude,
              m.lng longitude,
              m.orientacion orientation,
              m.velocidad speed
            FROM markers AS m
              JOIN vehicles AS v ON (v.plate = m.name )
              JOIN status_vehi AS sv ON (sv.id_status = m.status)              
              JOIN companies AS c ON (c.id = v.company_id)              
            WHERE
              m.fecha > current_date - $backDaysForSendSms AND (m.fecha||' '||m.hora)::TIMESTAMP < (current_timestamp - '$backTimeForSendSMS'::INTERVAL) AND (m.status = 1 OR m.status = 5) AND v.in_repair IS FALSE AND v.active IS TRUE
              
              AND (v.company_id = 14 OR v.company_id = 17 OR v.company_id = 21 OR v.company_id = 28 or v.company_id = 37)
              
              $companyQuery
              ORDER BY sv.des_status, (m.fecha||' '||m.hora)::TIMESTAMP DESC
        ";

        $downGPSList = DB::select($query);

        dd($downGPSList);

        if(count($downGPSList)){
            foreach ($downGPSList as $downGPS) {
                $vehicle = Vehicle::find($downGPS->vehicle_id);
                if($vehicle){
                    $response = SMS::sendResetCommandToVehicle($vehicle);
                    $this->info($response->log);
                }else{
                    $this->info("Vehicle not found: $downGPS->vehicle_id");
                }
            }
        }
        else{
            $this->info("Hey there are not down gps");
            $this->info($query);
        }
    }
}
