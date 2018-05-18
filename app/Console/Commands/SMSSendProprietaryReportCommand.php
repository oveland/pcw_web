<?php

namespace App\Console\Commands;

use App\CurrentDispatchRegister;
use App\CurrentSensorPassengers;
use App\DispatchRegister;
use App\Http\Controllers\API\SMS;
use App\Proprietary;
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
                    $time = $now->toTimeString();

                    $recorder = CounterByRecorder::reportByVehicle($vehicle->id,$dispatchRegisters,true);
                    $sensor = CurrentSensorPassengers::where('placa',$vehicle->plate)->get()->first();

                    $passengersByRecorder = $recorder->report->passengers;
                    $passengersBySensor = $sensor->passengers;

                    $message = "Pasajeros:\nHora: $time\nRegistradora: $passengersByRecorder\nSensor: $passengersBySensor\nRuta: $route->name\nVuelta: $currentDispatchRegister->round_trip";
                    SMS::sendCommand($message, $cellPhone);
                }
            }
        }
    }
}
