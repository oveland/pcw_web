<?php


namespace App\Services\GPS\Service4G;


use App\Models\Apps\Rocket\Photo;
use App\Models\Apps\Rocket\PhotoEvent;
use App\Models\Vehicles\GpsVehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use App\Services\GPS\Syrus\SyrusService;
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
use App\Models\Apps\Rocket\SyncStatus;

class Service4G extends SyrusService
{


    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    function syncPhoto($imei): Collection
    {
        if (!$this->readyToSync($imei)) return collect([
            'success' => false,
            'message' => " ~~~~ $imei is not ready to Sync",
        ]);

        $this->setStatus($imei, true);

        $service = new PhotoService();

        $gpsVehicle = GpsVehicle::where('imei', $imei)->first();

        if (!$gpsVehicle) return collect([
            'success' => false,
            'message' => "Imei $imei is not associated with a vehicle",
        ]);

        $vehicle = $gpsVehicle->vehicle;

        $waitSeconds = random_int(0, 240);
        Log::info("Sync photo from API GPS Syrus and vehicle $vehicle->number id: $vehicle->id in next $waitSeconds seconds");
        sleep($waitSeconds);
        Log::info("        • Start sync for vehicle $vehicle->number");

        $response = collect([
            'success' => true,
            'message' => "Success sync 4G",
        ]);

        $path = "$imei/192.168.1.45/2023-02-17";
        $response->put('imei', $imei);

        $storage = Storage::disk('Sync4G');
        $files = collect($storage->files($path));

        Log::info("         • Vehicle #$vehicle->number total FPT photos: " . $files->count());

        $saveFiles = collect([]);
        foreach ($files as $index => $file) {
            $fileName = collect(explode('/', $file))->last();

            if (Str::endsWith($file, '.jpg') && !Photo::where('uid', $file)->first()) {
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

                    $process = $service->saveImageData([
                        'date' => $date,
                        'img' => $image->encode('data-url'),
                        'type' => 'syrus',
                        'side' => $side,
                        'uid' => $fileName
                    ]);

                    $extra = "";
                    if ($process->response->success === true) {
                        $storage->delete($file);
                        if ($photoEvent) $photoEvent->delete();
                    } else {
                        $extra = $process->response->message;
                    }
                    Log::info("             • Vehicle #$vehicle->number saveImageData • #$index/" . $files->count() . " $extra");

                    $saveFiles->push($process->response->message);
                } else {
                    $storage->delete($file);
                    if ($photoEvent) $photoEvent->delete();
                }
            }
        }

        $response->put('sync', $saveFiles);

        $this->setStatus($imei, false);

        return $response;
    }

    function getSide($fileName, $imei)
    {
        $numberCamerasVehicle = 0;
        $gps = GpsVehicle::where('imei', $imei)->first();
        if ($gps) $numberCamerasVehicle = $gps->vehicle->cameras()->count();

        if ($numberCamerasVehicle == 1) return '1';
        

        return '0';
    }
}
