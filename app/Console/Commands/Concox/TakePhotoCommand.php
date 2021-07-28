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

        if ($camera == 2) sleep(10);

        $this->logData("Concox request and sync photo camera: $camera");
        $this->logData($this->concoxService->takePhoto($camera));
        sleep(15);
        $this->logData($this->concoxService->syncPhotos($camera, 90, 50));

        return null;
    }

    /**
     * @param $message
     * @param string $level
     */
    public function logData($message, string $level = 'info')
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
