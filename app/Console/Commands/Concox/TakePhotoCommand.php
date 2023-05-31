<?php

namespace App\Console\Commands\Concox;

use App\Services\Apps\Concox\ConcoxService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;
use Storage;

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
        $storage = Storage::disk('syrus');
        $files = collect($storage->files("352557104743383/images"));

        $files->map(function ($file) use ($storage) {
            dump($file. " xxxx ". Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString());
        });

        $files = $files->sortBy(function ($file) use ($storage) {
            return Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString();
        });

        $files->map(function ($file) use ($storage) {
            dump($file. " ---> ". Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString());
        });

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
