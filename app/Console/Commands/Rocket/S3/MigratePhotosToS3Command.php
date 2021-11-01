<?php

namespace App\Console\Commands\Rocket\S3;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Video\VideoService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Storage;

class MigratePhotosToS3Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:migrate-to-s3 {--vehicle-plate=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate photos to S3 Bucket';
    /**
     * @var VideoService
     */
    private $migrationService;

    /**
     * Create a new command instance.
     *
     * @param VideoService $videoService
     */
    public function __construct(VideoService $videoService)
    {
        parent::__construct();
        $this->migrationService = $videoService;
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

        if ($date) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $photos = Photo::whereVehicleAndDate($vehicle, $date)->get()->take(1000);

                $s3 = Storage::disk('s3');
                $local = Storage::disk('local');

                foreach ($photos as $photo) {

                    $originalPath = $photo->getOriginalPath();
                    $s3FilePath = $photo->buildPath('s3');

                    $migrated = true;

                    if (!$s3->exists($s3FilePath)) {
                        $migrated = $s3->put($s3FilePath, $local->get($originalPath));
                        $this->info("$originalPath: Put " . $originalPath . " >> $migrated");

                    } else {
                        $this->info("$s3FilePath exists!");
                    }

                    if($migrated){
                        $this->info("Photo data migrated: Disk: $photo->disk and path: $photo->path");
                        $photo->save();
                    }
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
