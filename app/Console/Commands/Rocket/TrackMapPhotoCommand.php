<?php

namespace App\Console\Commands\Rocket;

use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use Illuminate\Console\Command;

class TrackMapPhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:map:refresh {--vehicle-plate=DEM-003}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var PhotoService
     */
    private $photoService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->photoService = new PhotoService();
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
            $response = $this->photoService->for($vehicle)->notifyToMap();

            $this->info($response->message);
        } else {
            $this->info("Vehicle with plate $plate not found!");
        }
    }
}