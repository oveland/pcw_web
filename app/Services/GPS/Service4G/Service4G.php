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

        $waitSeconds = random_int(0, 40);
        $this->log("Sync photo from API GPS Syrus and vehicle $vehicle->number id: $vehicle->id in next $waitSeconds seconds");
        sleep($waitSeconds);
        $this->log("        • Start sync for vehicle $vehicle->number");

        $response = collect([
            'success' => true,
            'message' => "Success sync 4G",
        ]);
        $deviceID = $gpsVehicle->device_id;
        $date4G = carbon::now()->toDateString();
        $path = "$deviceID/$date4G";
        $response->put('imei', $imei);

        $storage = Storage::disk('Sync4G');
        $files = collect($storage->files($path));

        $this->log("         • Vehicle #$vehicle->number total FPT photos: " . $files->count());

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
                    $this->log("             • Vehicle #$vehicle->number saveImageData • #$index/" . $files->count() . " $extra");

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
        $fileNames = explode('_', $fileName);
        if ($imei = '352557104727915') {
            if ($fileNames[2] == 'ch1') return '1';
            if ($fileNames[2] == 'ch2') return '2';
            if ($fileNames[2] == 'ch3') return '4';
            if ($fileNames[2] == 'ch4') return '3';
            if ($fileNames[2] == 'ch5') return '5';
        }
        if ($fileNames[2] == 'ch3') return '1';
        if ($fileNames[2] == 'ch4') return '2';
        if ($fileNames[2] == 'ch5') return '3';
        if ($fileNames[2] == 'ch7') return '4';
        if ($fileNames[2] == 'ch8') return '5';

        return '0';
    }

    function log($message)
    {
        Log::channel('sync4g')->info("[Service4G] $message");
    }
}
