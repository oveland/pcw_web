<?php

namespace App\Console\Commands\Vehicles\NoReport;

use App\Models\Routes\CurrentDispatchRegister;
use App\Models\Passengers\CurrentSensorPassengers;
use App\Models\Routes\DispatchRegister;
use App\Http\Controllers\API\SMS;
use App\Models\Proprietaries\Proprietary;
use App\Traits\CounterByRecorder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SMSSendNoReportsCommand extends Command
{
    const ALERT_SMS_NUMBERS = [
        3108844273,
        3117132662
    ];
    private $issues = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-noReports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Vehiculo sin reportar GPS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->issues = collect([]);
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       // $this->issues = collect([]);
        $this->checkVehiculoStatus();


    }
    public function checkVehiculoStatus(){
        try {
            $checkStatus = DB::select("SELECT vehicle_status_id FROM current_locations WHERE vehicle_id = 2633 ")[0]->vehicle_status_id ?? null;
        } catch (\Exception $e) {
            dd('Error en la consulta SQL: ' . $e->getMessage());
        }
        if ($checkStatus == 1){
            var_dump('8419 no reporta');
            $this->issues->push("VH 8419 se encuentra sin reportar GPS");
            $this->sendAlerts();
        }
    }
    public function sendAlerts()
    {
        foreach ($this->issues as $issue) {
            foreach (self::ALERT_SMS_NUMBERS as $number) {
                SMS::sendCommand($issue, $number);
            }
        }
    }
}
