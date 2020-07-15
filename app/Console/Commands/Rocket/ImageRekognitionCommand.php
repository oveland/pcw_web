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
     */
    public function handle()
    {
        $date = $this->option('date');
        $vehiclePlate = $this->option('vehicle-plate');
        $type = $this->option('type');

        if ($date && $vehiclePlate) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $photos = Photo::findAllByVehicleAndDate($vehicle, $date);

                $this->info('Process ' . $photos->count() . ' photos with method: '.$type);

//                $ID = 4688;
//                DB::statement("UPDATE app_photos SET effects = null where id = $ID");
//                $photos = $photos->where('id', $ID);

                foreach ($photos as $photo) {
                    $prevDataRekognition = $type == 'persons' ? $photo->data_persons : $photo->data_faces;

                    $prevPassengers = $prevDataRekognition ? collect($prevDataRekognition->draws)->count() : 0;
                    $this->info("$photo->id $photo->date | With $prevPassengers passengers. " . $photo->encode('url') . "&with-effect=true&encode=png");

                    $photo->processRekognition(true, $type);
                    $photo->save();
                    $photo->refresh();

                    $nowDataRekognition = $type == 'persons' ? $photo->data_persons : $photo->data_faces;
                    $nowPassengers = $nowDataRekognition ? collect($prevDataRekognition->draws)->count() : 0;

                    $diff = $nowPassengers - $prevPassengers;
                    $this->info("       Now: $nowPassengers passengers | Diff = $diff" . (abs($diff) ? " *** CHANGED **** " : ""));
//                    $this->info(collect($photo->effects)->toJson());
                }
                $this->info("Finished!. Notifying to map...");
//                $this->photoService->for($vehicle)->notifyToMap($date);
            } else {
                $this->info("Plate $vehiclePlate doesnt associated with a vehicle!");
            }
        } else {
            $this->info('No date specified yet!');
        }

        return true;
    }
}
