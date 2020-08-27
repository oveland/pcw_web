<?php

namespace App\Console\Commands\Concox;

use App\Services\Apps\Concox\ConcoxService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TakePhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'concox:take-photo {--camera=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take a photo via Open API  to Concox device JC400';

    /**
     * @var ConcoxService
     */
    private $concoxService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->concoxService = new ConcoxService();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $camera = $this->option('camera');

        $this->info(Carbon::now(). " | Concox request photo camera: $camera");

        $response = $this->concoxService->takePhoto($camera);
        $this->info($response);

        $this->concoxService->syncPhotos();

        return null;
    }
}
