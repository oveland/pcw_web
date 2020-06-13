<?php

namespace App\Console\Commands\Rocket\S3;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use File;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Storage;

class DownloadPhotosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:s3:download-photos {--vehicle-plate=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a folder with photos associated to a dispatch register. This folder is used by AWS Custom Labels for training a rekognition model';

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
     * @return mixed
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $date = str_replace('-', '', $this->option('date'));
        $vehiclePlate = $this->option('vehicle-plate');
        if ($date && $vehiclePlate) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $remotePath = "/Apps/Rocket/Datasets/$vehicle->id/$date";

                // Convert to full paths
                $s3 = Storage::disk('s3');
                $local = Storage::disk('local');
                $remoteFiles = $s3->allFiles($remotePath);

                $this->info('Downloading from s3. ' . count($remoteFiles) . " files...");
                foreach ($remoteFiles as $remoteFilePath) {
                    $this->info("Download $remoteFilePath >> ");
                    $response = $local->put($remoteFilePath, $s3->get($remoteFilePath));
                }
                $this->info('Finished!');
            } else {
                $this->info("Plate $vehiclePlate doesnt associated with a vehicle!");
            }
        } else {
            $this->info('No date specified yet!');
        }
    }
}
