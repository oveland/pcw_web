<?php

namespace App\Console\Commands\Rocket\S3;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Tmp\MigrationService;
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

        if ($date) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $photos = Photo::findAllByVehicleAndDate($vehicle, $date)->take(5);

                $s3 = Storage::disk('s3');
                $local = Storage::disk('local');

                foreach ($photos as $photo) {

                    $originalPath = $photo->getOriginalPath();
                    $s3FilePath = $photo->buildPath('s3');

                    if (!$s3->exists($s3FilePath)) {
                        $response = $s3->put($s3FilePath, $local->get($originalPath));
                        $this->info("$originalPath: Put " . $originalPath . " >> $response");
                    } else {
                        $this->info("$s3FilePath exists!");
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
