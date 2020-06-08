<?php

namespace App\Console\Commands\Rocket;

use Carbon\Carbon;
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
    protected $signature = 'rocket:migrate-to-s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate photos to S3 Bucket';

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
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $localPath = '/Apps/Rocket/Photos/';
        $remotePath = '/Apps/Rocket/Photos/';

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

            $s3FilePath = "$remotePath/$vehicleId/$dateString/$timeString.jpeg";

            if (!$s3->exists($s3FilePath)) {
                $response = $s3->put($s3FilePath, Storage::get($pathFile));
                dump("$vehicleId, $dateString/$timeString.jpeg: Put " . $fileName . " >> $response");
            } else {
                dump("$s3FilePath exists!");
            }
        }
    }
}
