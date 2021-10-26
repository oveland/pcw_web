<?php


namespace App\Services\GPS\Syrus;


use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\GpsVehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Image;
use Intervention\Image\Exception\NotReadableException;
use Storage;
use Symfony\Component\ErrorHandler\Error\FatalError;

class SyrusService
{
    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    function syncPhoto($imei): Collection
    {
        $service = new PhotoService();


        $gpsVehicle = GpsVehicle::where('imei', $imei)->first();
        $vehicle = $gpsVehicle->vehicle;
        
        $response = collect([
            'success' => true,
            'message' => "Sync photo from API GPS Syrus and vehicle $vehicle->number id: $vehicle->id",
        ]);

        $path = "$imei/images";
        $response->put('imei', $imei);

        $storage = Storage::disk('syrus');
        $files = collect($storage->files($path));

        $saveFiles = collect([]);
        foreach ($files as $file) {
            $fileName = collect(explode('/', $file))->last();
            if (Str::endsWith($file, '.jpeg') && !Photo::where('uid', $file)->first()) {
                $side = $this->getSide($fileName, $imei);
                $service->for($vehicle, $side);

                $fileHasError = false;
                try {
                    $jpegInfo = exec("jpeginfo -c " . $storage->path($file));
                    $fileHasError = Str::contains($jpegInfo, "ERROR");
                } catch (Exception $e) {

                }

                if (!$fileHasError) {
                    $process = $service->saveImageData([
                        'date' => Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString(),
                        'img' => Image::make($storage->get($file))->encode('data-url'),
                        'type' => 'syrus',
                        'side' => $side,
                        'uid' => $fileName
                    ]);

                    if ($process->response->success === true) {
                        $storage->delete($file);
                    }

                    $saveFiles->push($process->response->message);
                }
            }
        }

        $response->put('sync', $saveFiles);

        return $response;
    }

    function getSide($fileName, $imei)
    {
        if (Str::startsWith($fileName, '1')) {
            return '1';
        } else if (Str::startsWith($fileName, '2')) {
            return '2';
        } else if (Str::startsWith($fileName, '3')) {
            return '3';
        }

        return '0';
    }
}
