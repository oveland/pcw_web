<?php


namespace App\Services\GPS\Syrus;


use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Photos\PhotoService;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Storage;
use Image;

class SyrusService
{
    /**
     * @throws FileNotFoundException
     */
    function syncPhoto($imei): Collection
    {
        $service = new PhotoService();

        $response = collect([
            'success' => true,
            'message' => 'Sync photo from API GPS Syrus',
        ]);

        $path = "$imei/images";
        $response->put('imei', $imei);
        $response->put('path', $path);

        $storage = Storage::disk('syrus');
        $files = collect($storage->files($path));

        $saveFiles = collect([]);
        foreach ($files as $file) {
            if (Str::endsWith($file, '.jpeg')) {
                try {
                    $image = Image::make($storage->get($file))->encode('data-url');
                    $service->for(Vehicle::find(1199));

                    $data = [
                        'date' => Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString(),
                        'img' => $image,
                        'type' => 'syrus',
                        'side' => Str::startsWith($file, '1') ? 'camera-1' : 'camera-2',
                        'uid' => $file
                    ];

                    $process = $service->saveImageData($data);
                    if ($process->response->success === true) {
                        $storage->delete($file);
                    }
                    $saveFiles->push($process->response->message);
                } catch (Exception $e) {

                }
            }
        }

        $response->put('sync', $saveFiles);

        return $response;
    }
}