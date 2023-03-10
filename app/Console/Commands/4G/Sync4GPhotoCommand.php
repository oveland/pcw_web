<?php

namespace App\Console\Commands\Syrus;

use App\Models\Company\Company;
use App\Services\GPS\Syrus\SyrusService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Sync4GPhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '4G:sync-photos {--imei=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command sync photo 4G';

    /**
     * @var Service4G
     */
    private $Service4G;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->Service4G = new Service4G();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $imei = $this->option('imei');
        $date = $this->option('date');


         if ($imei) {
            $response = $this->Service4G->syncPhoto($imei);
            $this->info($response);
        }
    }
}
