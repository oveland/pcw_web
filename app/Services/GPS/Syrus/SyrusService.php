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
use App\Models\Apps\Rocket\SyncStatus;

class SyrusService
{
    function readyToSync($imei)
    {
        $syncStatus = SyncStatus::where('imei', $imei)->first();
        if (!$syncStatus) return true;

        return !$syncStatus->busy || $syncStatus->updated_at->diffInMinutes() > 30;
    }

    function setStatus($imei, $busy)
    {
        $syncStatus = SyncStatus::where('imei', $imei)->first();
        if (!$syncStatus) $syncStatus = new SyncStatus(['imei' => $imei]);
        $syncStatus->busy = $busy;
        $syncStatus->save();
    }

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

        $waitSeconds = random_int(0, 15);

        $this->log("• Start sync for vehicle $vehicle->number in $waitSeconds");
        sleep($waitSeconds);

        $response = collect([
            'success' => true,
            'message' => "Success sync",
        ]);

        $path = "$imei/images";
        $response->put('imei', $imei);

        $storage = Storage::disk('syrus');
        $files = collect($storage->files($path))->sortBy(function ($file) use ($storage) {
            return Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString();
        });

        $this->log("    • Vehicle #$vehicle->number ($imei) total FPT photos: " . $files->count());


        $saveFiles = collect([]);
        foreach ($files as $index => $file) {
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

                    $success = $process->response->success;
                    $message = $process->response->message;

                    $extra = "";
                    if ($success === true) {
                        $deleted = $storage->delete($file);
                        if (!$deleted) $extra = ". Error photo NOT deleted!";
                        if ($photoEvent) $photoEvent->delete();
                        $message .= $extra;
                    } else {
                        $extra = $message;
                    }

                    $this->log("             • Vh #$vehicle->number($vehicle->id)[$imei] photo $fileName saved($success): #" . ($index + 1) . "/" . $files->count() . " $extra");

                    $response['success'] = $success;
                    $response['message'] = $message;

                    $saveFiles->push($message);
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

        if ($imei == '352557100791261') {
            if (Str::startsWith($fileName, '1')) {
                return '1';
            } else if (Str::startsWith($fileName, '3')) {
                return '2';
            } else if (Str::startsWith($fileName, '2')) {
                return '3';
            }
        }
        if ($imei == '352557104791572') {
            if (Str::startsWith($fileName, '1')) {
                return '2';
            } else if (Str::startsWith($fileName, '2')) {
                return '1';
            } else if (Str::startsWith($fileName, '3')) {
                return '3';
            }
        }


        if ($imei == '352557104727170') {
            if (Str::startsWith($fileName, '2')) {
                return '1';
            } else if (Str::startsWith($fileName, '3')) {
                return '2';
            } else if (Str::startsWith($fileName, '1')) {
                return '3';
            }
        }


        if ($imei == '352557104789550') {
            if (Str::startsWith($fileName, '1')) {
                return '1';
            } else if (Str::startsWith($fileName, '2')) {
                return '3';
            } else if (Str::startsWith($fileName, '3')) {
                return '2';
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

    function log($message)
    {
        Log::channel('sync3g')->info("[SyrusService] $message");
    }
}
