<?php

namespace App\Console\Commands\Rocket;

use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\RocketService;
use Illuminate\Console\Command;

class TakePhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:take-photo {--vehicle-plate=DEM-003} {--side=rear} {--quality=hd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send command to Rocket App for take a photo via Web Sockets connection';

    /**
     * @var RocketService
     */
    private $rocketService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->rocketService = new RocketService();
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
            $response = $this->rocketService->for($vehicle)
                ->command([
                    'action' => 'get-photo',
                    'side' => $this->option('side'),
                    'quality' => $this->option('quality')
                ]);

            $this->info($response->message);
            $this->info("Command: ". collect($response->params)->toJson());
        } else {
            $this->info("Vehicle with plate $plate not found!");
        }
    }
}
