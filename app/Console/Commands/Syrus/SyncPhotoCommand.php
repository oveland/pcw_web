<?php

namespace App\Console\Commands\Syrus;

use App\Services\GPS\Syrus\SyrusService;
use Illuminate\Console\Command;

class SyncPhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syrus:sync-photos {--imei=357042066532541}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var SyrusService
     */
    private $syrusService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->syrusService = new SyrusService();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $imei = $this->option('imei');
        $response = $this->syrusService->syncPhoto($imei);

        $this->info($response);
    }
}
