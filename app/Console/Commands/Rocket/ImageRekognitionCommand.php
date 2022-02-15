<?php

namespace App\Console\Commands\Rocket;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use Illuminate\Console\Command;

class ImageRekognitionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:image:rekognition {--vehicle-plate=} {--date=} {--type=persons_and_faces} {--camera=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return bool
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
//                    ->where('dispatch_register_id', '>', 0)
//                    ->where('id', '<=', 117340)
                    ->where('date', '>=', '2022-02-09 15:00:00')
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
            } else {
                $this->info("Plate $vehiclePlate doesnt associated with a vehicle!");
            }
        } else {
            $this->info('No date specified yet!');
        }

        return true;
    }
}
