<?php

namespace App\Console\Commands\Rocket;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use App\Services\AWS\RekognitionService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ImageRekognitionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:image:rekognition {--vehicle-plate=} {--date=} {--type=persons}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var RekognitionService
     */
    private $rekognition;
    /**
     * @var PhotoService
     */
    private $photoService;

    /**
     * Create a new command instance.
     *
     * @param RekognitionService $rekognition
     * @param PhotoService $photoService
     */
    public function __construct(RekognitionService $rekognition, PhotoService $photoService)
    {
        parent::__construct();
        $this->rekognition = $rekognition;
        $this->photoService = $photoService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $date = $this->option('date');
        $vehiclePlate = $this->option('vehicle-plate');
        if ($date && $vehiclePlate) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $photos = Photo::whereDate('date', $date)->where('vehicle_id', $vehicle->id)->get();
                $this->info('Process ' . $photos->count() . ' photos');
//                $ID = 4688;
//                DB::statement("UPDATE app_photos SET effects = null where id <> $ID");
//                $photos = $photos->where('id', $ID);

                foreach ($photos as $photo) {
                    $prevPersons = $photo->persons;
                    $this->info("$photo->id | Process $photo->path with $photo->persons persons. " . $photo->encode('url'));

                    $photo->effects = [
                        'brightness' => 10,
                        'contrast' => 5,
                        'gamma' => 2,
                        'sharpen' => 12
                    ];

                    $photo->processRekognition(true, 'persons');
                    $photo->save();

                    $diff = $photo->persons - $prevPersons;
                    $this->info("       Now: $photo->persons persons | Diff = $diff" . (abs($diff) ? " ******* " : ""));
                    $this->info(collect($photo->effects)->toJson());
                }
                $this->info("Finished!. Notifying to map...");
                $this->photoService->notifyToMap($vehicle, $date);

            } else {
                $this->info("Plate $vehiclePlate doesnt associated with a vehicle!");
            }
        } else {
            $this->info('No date specified yet!');
        }

        return true;
    }
}
