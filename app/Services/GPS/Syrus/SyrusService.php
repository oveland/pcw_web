<?php


namespace App\Services\GPS\Syrus;


use App\Models\Apps\Rocket\Photo;
use App\Models\Apps\Rocket\PhotoEvent;
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
use Log;

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

        $message = "Sync photo from API GPS Syrus and vehicle $vehicle->number id: $vehicle->id";

        Log::info($message);

        $response = collect([
            'success' => true,
            'message' => $message,
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

                $date = Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString();

                $photoEvent = PhotoEvent::whereImei($imei)->whereUid($fileName)->first();
                if ($photoEvent) {
                    $date = $photoEvent->date->toDateTimeString();
                }

                if (!$fileHasError) {
                    $image = Image::make($storage->get($file));

                    /*if ($vehicle->id == 1873 && intval($side) === 2) { // Corrige el giro de la c?mara vh 02 Montebello
                        $image = $image->rotate(180);
                    }*/

                    $process = $service->saveImageData([
                        'date' => $date,
                        'img' => $image->encode('data-url'),
                        'type' => 'syrus',
                        'side' => $side,
                        'uid' => $fileName
                    ]);

                    if ($process->response->success === true) {
                        $storage->delete($file);
                        if ($photoEvent) $photoEvent->delete();
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
        $numberCamerasVehicle = 0;
        $gps = GpsVehicle::where('imei', $imei)->first();
        if ($gps) $numberCamerasVehicle = $gps->vehicle->cameras()->count();

        if($numberCamerasVehicle == 1) return '1';

        if ($imei == '352557100791261') {
            if (Str::startsWith($fileName, '1')) {
                return '1';
            } else if (Str::startsWith($fileName, '3')) {
                return '2';
            } else if (Str::startsWith($fileName, '2')) {
                return '3';
            }
        }

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
