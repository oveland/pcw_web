<?php

namespace App\Console\Commands\Rocket;

use Artisan;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Console\Command;

class CountDateRangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:count-range {--vehicle-plate=} {--type=persons_and_faces} {--camera=all} {--pa=2} {--pr=2} {--from=} {--to=}';

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
     * @return int
     */
    public function handle()
    {
        $vehiclePlate = $this->option('vehicle-plate');
        $type = $this->option('type');
        $camera = $this->option('camera');
        $pa = $this->option('pa');
        $pr = $this->option('pr');
        $from = $this->option('from');
        $to = $this->option('to');

        $period = new DatePeriod(
            new DateTime($from),
            new DateInterval('P1D'),
            new DateTime($to)
        );

        foreach ($period as $date) {
            $date = $date->format('Y-m-d');
            $command = "rocket:count --vehicle-plate=$vehiclePlate --type=$type --camera=$camera --pa=$pa --pr=$pr --date=$date";
            $this->info("Executing > $command");
            Artisan::call($command);
        }
        
        $this->info("End success");
    }
}
