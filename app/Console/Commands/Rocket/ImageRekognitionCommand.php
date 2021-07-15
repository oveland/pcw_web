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
    protected $signature = 'rocket:image:rekognition {--vehicle-plate=} {--date=} {--type=persons} {--camera=0}';

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
        $camera = $this->option('camera');

        if ($date && $vehiclePlate) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $photos = Photo::whereVehicleAndDateAndSide($vehicle, $date, $camera)
//                    ->where('dispatch_register_id', 1276931) // Round trip 1 322 October 2nd
                    ->where('id', '>=',77333)
                    ->orderBy('date')
                    ->get();

                $total = $photos->count();
                $index = 1;

                $this->info("Process $total photos with method: $type");

                foreach ($photos as $photo) {
                    $completed = number_format($index * 100 / $total, 1);

                    $prevDataRekognition = $photo->data_persons;
                    $prevPassengers = $prevDataRekognition ? collect($prevDataRekognition->draws)->count() : 0;

                    $this->info("$index of $total - $completed% > $photo->id $photo->date | With $prevPassengers recognitions. " . $photo->encode('url') . "&with-effect=true&encode=png");

                    $photo->processRekognition(true, $type);
                    $photo->save();
                    $photo->refresh();

                    $nowDataRekognition = $photo->data_persons;
                    $nowPassengers = $nowDataRekognition ? collect($nowDataRekognition->draws)->count() : 0;

                    $diff = $nowPassengers - $prevPassengers;
                    $this->info("       Now: $nowPassengers recognitions | Diff = $diff" . (abs($diff) ? " *** CHANGED **** " : ""));

                    $index++;
                }
                $this->info("Finished! **** ");
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
