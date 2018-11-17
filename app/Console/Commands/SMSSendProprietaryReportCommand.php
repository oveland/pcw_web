<?php

namespace App\Console\Commands;

use App\Models\Routes\CurrentDispatchRegister;
use App\Models\Passengers\CurrentSensorPassengers;
use App\Models\Routes\DispatchRegister;
use App\Http\Controllers\API\SMS;
use App\Models\Proprietaries\Proprietary;
use App\Traits\CounterByRecorder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SMSSendProprietaryReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-proprietary-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send passenger report to proprietary via SMS';

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
        $this->info('Sending passengers reports to proprietaries');
        $now = Carbon::now();
        $proprietaries = Proprietary::where('passenger_report_via_sms', true)->get();

        foreach ($proprietaries as $proprietary) {
            $company = $proprietary->company;
            $cellPhone = $proprietary->cellphone;

            $assignedVehicles = $proprietary->assignedVehicles;

            foreach ($assignedVehicles as $assignation) {
                $vehicle = $assignation->vehicle;

                $dispatchRegisters = DispatchRegister::where('date', $now->toDateString())
                    ->where('vehicle_id', $vehicle->id)
                    ->completed()
                    ->orderBy('departure_time')
                    ->get();

                if( count($dispatchRegisters) ){
                    $currentDispatchRegister = $dispatchRegisters->last();
                    $route = $currentDispatchRegister->route;
                    $arrivalTime = explode('.', $currentDispatchRegister->arrival_time)[0];

                    $recorder = CounterByRecorder::reportByVehicle($vehicle->id,$dispatchRegisters,true);
                    $sensor = CurrentSensorPassengers::where('placa',$vehicle->plate)->get()->first();
                    $timeSensor = explode('.', $sensor->hora_status)[0]; // TODO Change column when table contador is migrated

                    $passengersByRecorder = $recorder->report->passengers;
                    $passengersBySensor = $sensor->passengers;

                    $message = $reportSMS = collect([
                        'prop' => true,                                 // Indicates that data SMS belongs to a proprietary
                        'vn' => $vehicle->number,
                        'pr' => $passengersByRecorder,                  // Passengers by Recorder
                        'ps' => $passengersBySensor,                    // Passengers by Sensor
                        'tr' => $arrivalTime,                           // Time by Recorder
                        'ts' => $timeSensor,                            // Time by Sensor,
                        'rn' => $route->name,                           // Route name,
                        'rr' => $currentDispatchRegister->round_trip,   // Route round trip,
                        'rt' => $currentDispatchRegister->turn,         // Route turn,
                    ])->toJson();

                    $message = str_replace('{','(',$message);
                    $message = str_replace('}',')',$message);

                    //$message = "Pasajeros $vehicle->number:\nRegistradora ($arrivalTime): $passengersByRecorder\nSensor ($timeSensor): $passengersBySensor\nRuta: $route->name\nVuelta: $currentDispatchRegister->round_trip";
                    SMS::sendCommand($message, $cellPhone);
                    $this->info("Message ($cellPhone):\n$message");
                }
            }
        }
    }
}
