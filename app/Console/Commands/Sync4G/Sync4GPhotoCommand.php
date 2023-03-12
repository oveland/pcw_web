<?php

namespace App\Console\Commands\Sync4G;

use App\Models\Company\Company;
use App\Services\GPS\Service4G\Service4G;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class
Sync4GPhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sync4G:sync-photos {--imei=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command sync photo Sync4G';

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
        $date4G = $date ?: Carbon::now()->toDateString();
         if ($imei) {
            $response = $this->Service4G->syncPhoto($imei,$date4G);
            $this->info($response);
        }
    }
}
