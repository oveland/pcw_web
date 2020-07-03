<?php

namespace App\Console\Commands\Rocket;

use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\RocketService;
use Illuminate\Console\Command;

class ReleaseBusyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:release-busy {--vehicle-plate=DEM-003}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send command to Rocket App for release app when app is blocked by prevent kill down';

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
                    'action' => 'set-enable',
                    'release' => 'true',
                ]);

            $this->info($response->message);
            $this->info("Command: ". collect($response->params)->toJson());
        } else {
            $this->info("Vehicle with plate $plate not found!");
        }
    }
}
