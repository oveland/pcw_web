<?php

namespace App\Console\Commands\Rocket\S3;

use App\Models\Apps\Rocket\Photo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class CreateSamplesForRekognitionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:s3:create-samples-for-rekognition';

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
        $localPath = '/Apps/Rocket/Photos/';
        $remotePath = '/Apps/Rocket/Models/';

        $s3 = Storage::disk('s3');
        $local = Storage::disk('local');

        $localFiles = collect($local->allFiles($localPath))->take(2);

        foreach ($localFiles as $pathFile) {
            $data = collect(explode('/', $pathFile));

            $fileName = $data->get(4);
            $vehicleId = $data->get(3);
            $dateTime = Carbon::parse(explode('.', $fileName)[0]);
            $dateString = $dateTime->format('Ymd');
            $timeString = $dateTime->format('His');

            $photo = Photo::where('vehicle_id', $vehicleId)->where('path', $pathFile)->first();

            if ($photo->dispatch_register_id && $photo->persons) {
                $dr = $photo->dispatchRegister;
                $route = $dr->route;

                $s3FilePath = "$remotePath/$vehicleId/$dateString/$timeString.jpeg";
                $response = $s3->put($s3FilePath, Storage::get($pathFile));
                dump("$vehicleId, $route->name, RT: $dr->round_trip | $dateString/$timeString.jpeg >> $photo->persons persons");
            }
        }
    }
}
