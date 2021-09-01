<?php

namespace App\Console\Commands\Rocket;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:count {--vehicle-plate=WDL-057} {--type=persons_and_faces} {--camera=all} {--pa=2} {--pr=2} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process passengers count of saved photos';

    /**
     * @var PhotoService
     */
    private $photoService;

    /**
     * Create a new command instance.
     *
     */
    public function __construct(PhotoService $photoService)
    {
        parent::__construct();
        $this->photoService = $photoService;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $date = $this->option('date') ?? Carbon::now()->toDateString();
        $vehiclePlate = $this->option('vehicle-plate');
        $persistenceActivate = $this->option('pa');
        $persistenceRelease = $this->option('pr');
        $camera = $this->option('camera');

        if ($date && $vehiclePlate) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $this->log("Start process count: Vehicle = $vehicle->number • Camera = $camera • Date = $date");
                $response = $this->photoService->for($vehicle, $camera, $persistenceActivate, $persistenceRelease)->getHistoric($date, true);
                $this->log($response->toJson());
                $this->log("Count finished! **** ");
            } else {
                $this->log("Plate $vehiclePlate doesnt associated with a vehicle!");
            }
        } else {
            $this->log('No date specified yet!');
        }

        return true;
    }

    /**
     * @param $message
     */
    function log($message)
    {
        $date = Carbon::now()->toDateTimeString();
        $this->info("$date • $message");
    }
}
