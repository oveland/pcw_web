<?php

namespace App\Console\Commands\Rocket\S3;

use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Video\VideoService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class VideoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:s3:video {--vehicle-plate=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a folder with photos associated to a dispatch register. This folder is used by AWS Custom Labels for training a rekognition model';
    /**
     * @var VideoService
     */
    private $videoService;

    /**
     * Create a new command instance.
     *
     * @param VideoService $videoService
     */
    public function __construct(VideoService $videoService)
    {
        parent::__construct();
        $this->videoService = $videoService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $date = $this->option('date');
        $vehiclePlate = $this->option('vehicle-plate');
        if ($date && $vehiclePlate) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $nowInitial = Carbon::now();

                $this->log("Start download");

                $this->videoService->for($vehicle, $date);

                $now = Carbon::now();
//                $this->videoService->downloadPhotos();
                $this->log("    DownloadPhotos " . Carbon::now()->from($now));

                $now = Carbon::now();
//                $this->videoService->processPhotos();
                $this->log("    processPhotos " . Carbon::now()->from($now));

                $now = Carbon::now();
                $this->videoService->processVideo();
                $this->log("    processVideo " . Carbon::now()->from($now));

                $totalDuration = Carbon::now()->from($nowInitial);
                $this->log("Finished $totalDuration");
            } else {
                $this->log("Plate $vehiclePlate doesnt associated with a vehicle!");
            }
        } else {
            $this->log('No date specified yet!');
        }
    }

    public function log($message)
    {
        $now = Carbon::now();

        $this->info("$now • $message");
        Log::info($message);
    }
}
