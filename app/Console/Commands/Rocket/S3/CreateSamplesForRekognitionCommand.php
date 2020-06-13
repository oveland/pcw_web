<?php

namespace App\Console\Commands\Rocket\S3;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Storage;

class CreateSamplesForRekognitionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:s3:create-samples-for-rekognition {--vehicle-plate=} {--date=}';

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
     */
    public function handle()
    {
        $date = $this->option('date');
        $vehiclePlate = $this->option('vehicle-plate');
        if ($date && $vehiclePlate) {
            $vehicle = Vehicle::where('plate', $vehiclePlate)->first();

            if ($vehicle) {
                $remotePath = '/Apps/Rocket/Datasets/';

                $s3 = Storage::disk('s3');

                $photos = Photo::where('vehicle_id', $vehicle->id)
                    ->whereDate('date', $date)
                    ->whereDate('dispatch_register_id', '<>', null)
                    ->whereDate('persons', '>', 0)
                    ->get();

                foreach ($photos as $photo) {
                    $data = collect(explode('/', $photo->path));

                    $fileName = $data->get(4);
                    $vehicleId = $data->get(3);
                    $dateTime = Carbon::parse(explode('.', $fileName)[0]);
                    $dateString = $dateTime->format('Ymd');
                    $timeString = $dateTime->format('His');

                    if ($photo && $photo->dispatch_register_id && $photo->persons) {
                        $dr = $photo->dispatchRegister;
                        $route = $dr->route;

                        $s3FilePath = "$remotePath/$vehicleId/$dateString/$timeString-$photo->persons.jpeg";
                        $response = $s3->put($s3FilePath, Storage::get($photo->path));
                        $this->info("$vehicleId, $route->name, RT: $dr->round_trip | $dateString/$timeString-$photo->persons.jpeg >> Persons: $photo->persons");
                    }
                }
            } else {
                $this->info("Plate $vehiclePlate doesnt associated with a vehicle!");
            }
        } else {
            $this->info('No date specified yet!');
        }
    }
}
