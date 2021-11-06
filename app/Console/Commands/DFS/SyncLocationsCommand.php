<?php

namespace App\Console\Commands\DFS;

use App;
use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use Illuminate\Console\Command;

class SyncLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dfs:sync:locations {--date=} {--company=39} {--type=} {--vehicle-plate=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var App\Services\LM\LMService
     */
    private $service;

    private $company;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    function init()
    {
        $this->company = Company::find($this->option('company'));

        $this->info("Process locations for company " . $this->company->name);

        $this->service = App::makeWith('lm.service', ['company' => $this->company->id, 'console' => true]);

        if (!$this->service->sync) {
            $this->error("Not sync service available");
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->init();

        $processVehicles = collect([]);

        $date = $this->option('date');
        $type = $this->option('type');

        $this->warn("Process date $date");

        if ($type === 'all') {
            $processVehicles = $this->service->repository->getAllVehicles(true);
        } else {
            $vehicle = Vehicle::wherePlate($this->option('vehicle-plate'))->first();
            if ($vehicle) {
                if ($vehicle->belongsToCompany($this->company)) {
                    $processVehicles->push($vehicle);
                } else {
                    $this->error("Vehicle not belongs to company :(");
                }
            } else {
                $this->error("Vehicle not found");
            }
        }


        if ($processVehicles->count()) {
            foreach ($processVehicles as $vehicle) {
                $this->service->sync->locations($vehicle, $date);
            }
        } else {
            $this->warn("There are not vehicles to process");
        }
    }
}
