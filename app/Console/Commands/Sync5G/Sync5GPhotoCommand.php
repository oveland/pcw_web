<?php

namespace App\Console\Commands\sync5G;

use App\Services\GPS\Syrus5G\Service5G;
use Illuminate\Console\Command;

class Sync5GPhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync5G:sync-photos {--imei=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var Service5G
     */
    private $service5G;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service5G = new Service5G();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $imei = $this->option('imei');
        $date = $this->option('date');
        //$date4G = $date ?: Carbon::now()->toDateString();
        if ($imei) {
            $response = $this->service5G->syncPhoto($imei);
            $this->info($response);
        }
    }
}
