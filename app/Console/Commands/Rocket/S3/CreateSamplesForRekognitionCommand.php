<?php

namespace App\Console\Commands\Rocket\S3;

use App\Models\Apps\Rocket\Photo;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateSamplesForRekognitionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:s3:create-samples-for-rekognition {--date}';

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
        if ($date) {
            $localPath = '/Apps/Rocket/Photos/';
            $remotePath = '/Apps/Rocket/Datasets/';

            $s3 = Storage::disk('s3');
            $local = Storage::disk('local');

            $localFiles = collect($local->allFiles($localPath));

            foreach ($localFiles as $pathFile) {
                $data = collect(explode('/', $pathFile));

                $fileName = $data->get(4);
                $vehicleId = $data->get(3);
                $dateTime = Carbon::parse(explode('.', $fileName)[0]);
                $dateString = $dateTime->format('Ymd');
                $timeString = $dateTime->format('His');

                $photo = Photo::where('vehicle_id', $vehicleId)
                    ->whereDate('date', $date)
                    ->where('path', $pathFile)->first();

                if ($photo->dispatch_register_id && $photo->persons) {
                    $dr = $photo->dispatchRegister;
                    $route = $dr->route;

                    $s3FilePath = "$remotePath/$vehicleId/$dateString/$timeString-$photo->persons.jpeg";
                    $response = $s3->put($s3FilePath, Storage::get($pathFile));
                    $this->info("$vehicleId, $route->name, RT: $dr->round_trip | $dateString/$timeString.jpeg >> $photo->persons persons");
                }
            }
        } else {
            $this->info('No date specified yet!');
        }
    }
}
