<?php


namespace App\Services\Apps\Rocket\Tmp;


use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Log;
use Storage;

class MigrationService
{
    /**
     * @param Vehicle $vehicle
     * @param $date
     * @throws FileNotFoundException
     */
    public function downloadPhotoFromS3(Vehicle $vehicle, $date)
    {
        // Convert to full paths
        $local = Storage::disk('local');
        $s3 = Storage::disk('s3');

        $remotePath = "/Apps/Rocket/Datasets/$vehicle->id/$date";
        $remoteFiles = $s3->allFiles($remotePath);

        $this->info('Downloading from s3. ' . count($remoteFiles) . " files...");
        foreach ($remoteFiles as $remoteFilePath) {
            $this->info("Download $remoteFilePath >> ");
            $response = $local->put($remoteFilePath, $s3->get($remoteFilePath));
        }
    }

    public function info($message)
    {
        Log::info($message);
    }
}