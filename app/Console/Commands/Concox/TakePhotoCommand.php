<?php

namespace App\Console\Commands\Concox;

use App\Services\Apps\Concox\ConcoxService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

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

        $camera = '1';
        $this->logData(Carbon::now() . " | Concox request photo camera: $camera");
        $response = $this->concoxService->takePhoto($camera);
        $this->logData($response);

        $this->info(Carbon::now() . " | Sync: $camera");
        $this->info($this->concoxService->syncPhotos($camera, 60, 30));

        Log::info(" | Sync: $camera");

//        $camera = '2';
//        $this->logData(Carbon::now() . " | Concox request photo camera: $camera");
//        $response = $this->concoxService->takePhoto($camera);
//        $this->logData($response);

        return null;
    }

    /**
     * @param $message
     * @param string $level
     */
    public function logData($message, $level = 'info')
    {
        $this->info($message);
        switch ($level) {
            case 'warning':
                Log::warning($message);
                break;
            case 'error':
                Log::error($message);
                break;
            default:
                Log::info($message);
                break;
        }
    }
}
