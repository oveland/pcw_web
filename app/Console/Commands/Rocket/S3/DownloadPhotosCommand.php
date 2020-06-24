<?php

namespace App\Console\Commands\Rocket\S3;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Tmp\MigrationService;
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
     * @var MigrationService
     */
    private $migrationService;

    /**
     * Create a new command instance.
     *
     * @param MigrationService $migrationService
     */
    public function __construct(MigrationService $migrationService)
    {
        parent::__construct();
        $this->migrationService = $migrationService;
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
                if($this->option('from') === 's3'){
                    $this->migrationService->downloadPhotoFromS3($vehicle, $date);
                }else{
                    $this->migrationService->downloadPhotoFromS3($vehicle, $date);
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
