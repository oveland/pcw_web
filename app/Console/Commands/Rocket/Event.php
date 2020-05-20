<?php

namespace App\Console\Commands\Rocket;

use App\Events\App\RocketAppEvent;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Event extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:event {--action=get-photo} {--side=rear} {--quality=hd} {--vehicle-plate=DEM-003}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $plate = $this->option('vehicle-plate');
        $vehicle = Vehicle::where('plate', $plate)->first();

        if ($vehicle) {
            $action = $this->option('action');
            if ($action) {
                $side = $this->option('side');
                $this->info($side);

                if (collect(['rear', 'front'])->contains($side)) {
                    $options = collect([
                        'action' => $action,
                        'side' => $side,
                        'quality' => $this->option('quality')
                    ]);

                    $this->info(Carbon::now()." | $vehicle->plate: Requesting event with data: ".$options->toJson());
                    
                    event(new RocketAppEvent($vehicle, $options->toArray()));
                }else {
                    $this->info("Camera side is invalid");
                }
            } else {
                $this->info("Action not found");
            }
        } else {
            $this->info("Vehicle with plate $plate not found!");
        }
    }
}
