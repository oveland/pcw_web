<?php

namespace App\Services\GPS\Syrus5G;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\GpsVehicle;
use App\Services\Apps\Rocket\Photos\SavePhotoService;
use App\Services\GPS\Syrus\SyrusService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Image;
use Log;
use Storage;

class Service5GPhotoV2 extends SyrusService
{
    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    function syncPhotoV2(GpsVehicle $gpsVehicle): Collection
    {
        $service = new SavePhotoService();
        $vehicle = $gpsVehicle->vehicle;

        $this->log("        • Start sync for vehicle $vehicle->number");

        $response = collect([
            'success' => true,
            'message' => "Success sync 5G V2",
        ]);
        /*if ($vehicle->number=='8401'){
           $date5G ='2025-11-24';
        }else{
            $date5G   = Carbon::now()->toDateString();
        }*/

        $date5G   = Carbon::now()->toDateString();

        $storage  = Storage::disk('Sync4G');
        $saveFiles = collect([]);

        $deviceIds = collect([
            $gpsVehicle->device_id,
            $gpsVehicle->device_id_2,
            $gpsVehicle->device_id_3,
        ])->filter();

        foreach ($deviceIds as $deviceID) {
            $path = "$deviceID/$date5G";

            // ✅ Primero validar que exista el path (evita IO innecesario)
            if (!$storage->exists($path)) {
                $this->log("• Path no encontrado para device: $deviceID");
                continue;
            }

            $files = collect($storage->files($path));
            $this->log("• Vehicle #{$vehicle->number} - Device $deviceID total photos: " . $files->count());

            foreach ($files as $index => $file) {
                $fileName = collect(explode('/', $file))->last();

                // ✅ Procesar solo T.jpg y E.jpg
                if (!Str::endsWith($file, ['T.jpg', 'E.jpg'])) {
                    continue;
                }

                // ✅ Tipo y side
                $isE  = Str::endsWith($file, 'E.jpg');
                $type = $isE ? 'E' : 'T';
                $side = $this->getSideV2($fileName, $gpsVehicle, $deviceID);

                // ✅ UID consistente (y usarlo tanto para consultar como para guardar)
                $uid = $vehicle->number . "_" . $fileName;

                // Evitar reprocesar
                if (Photo::where('uid', $uid)->exists()) {
                    continue;
                }

                $service->for($vehicle, $side);

                // Validar/chequear archivo (jpeginfo)
                $fileHasError = false;
                try {
                    $jpegInfo = exec("jpeginfo -c " . escapeshellarg($storage->path($file)));
                    $fileHasError = Str::contains($jpegInfo, "ERROR");
                } catch (Exception $e) {
                    // opcional: loguear error de verificación
                }

                // Fecha desde nombre (preferida) o mtime del archivo
                $fileNames = explode('_', $fileName);
                $dateImag  = '';
                if (isset($fileNames[2]) && preg_match('/^\d{14}$/', $fileNames[2])) {
                    $dateImag = Carbon::createFromFormat("YmdHis", $fileNames[2])->toDateTimeString();
                } elseif (isset($fileNames[3]) && preg_match('/^\d{14}$/', $fileNames[3])) {
                    $dateImag = Carbon::createFromFormat("YmdHis", $fileNames[3])->toDateTimeString();
                }
                $date = $dateImag === ''
                    ? Carbon::createFromTimestamp($storage->lastModified($file))->toDateTimeString()
                    : $dateImag;

                if ($fileHasError) {
                    // Archivo corrupto: eliminar y seguir
                    $storage->delete($file);
                    continue;
                }

                // Cargar contenido y enviar a SavePhotoService
                $image = Image::make($storage->get($file));
                $process = $service->saveImageData([
                    'date'       => $date,
                    'img'        => $image->encode('data-url'),
                    'type'       => $type,         // ✅ T o E (no 'syrus')
                    'side'       => $side,         // para E no es obligatorio (validador en SavePhotoService)
                    'uid'        => $uid,          // ✅ consistente
                    'file_type'  => $type,         // T | E
                    'file_name'  => $fileName,
                ], true); // ✅ pedir retorno de 'photo' (getAPIFields)


                $success = $process->response->success ?? false;
                $message = $process->response->message ?? '';
                $extra   = "";

                if ($success === true) {

                    // ✅ Guardar en file_names aquí mismo (sin 'path')
                    //    Tomamos DR desde la respuesta si viene; si no, null.
                    $photoPayload = (array)($process->photo ?? []);
                    $dispatchId   = $photoPayload['dispatch_register_id'] ?? null;

                    DB::table('file_names')->insertOrIgnore([
                        'file_name'            => $fileName,     // solo nombre original
                        'vehicle_id'           => $vehicle->id,
                        'dispatch_register_id' => $dispatchId,
                        'file_type'            => $type,         // T o E
                        'date'                 => $date,         // fecha de la foto
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);

                    // Eliminar archivo de origen si todo fue bien
                    $deleted = $storage->delete($file);
                    if (!$deleted) {
                        $extra = ". Error photo NOT deleted!";
                    }
                    $message .= $extra;
                } else {
                    $extra = $message;
                }

                $this->log("             • Vehicle #$vehicle->number saveImageData • #$index/" . $files->count() . " $extra" . $message);
                $response['success'] = $success;
                $response['message'] = $message;
                $saveFiles->push($message);
            }
        }

        $response->put('sync', $saveFiles);
        return $response;
    }

    function getSideV2($fileName, GpsVehicle $gpsVehicle, $deviceId)
    {
        $fileNames = explode('_', $fileName);

        if (Str::endsWith($fileName, ['E.jpg'])) {
            return 'E';
        }

        $channel = $fileNames[1] ?? null;
        if (!$channel || !preg_match('/^ch\d+$/', $channel)) {
            return '0';
        }

        $deviceNumber = null;
        if ($deviceId == $gpsVehicle->device_id) {
            $deviceNumber = 1;
        } elseif ($deviceId == $gpsVehicle->device_id_2) {
            $deviceNumber = 2;
        } elseif ($deviceId == $gpsVehicle->device_id_3) {
            $deviceNumber = 3;
        }

        $map = [
            1 => ['ch1' => 1, 'ch2' => 2, 'ch3' => 7, 'ch4' => 8],
            2 => ['ch1' => 3, 'ch2' => 4],
            3 => ['ch1' => 5, 'ch2' => 6, 'ch3' => 7, 'ch4' => 8],
        ];

        return $map[$deviceNumber][$channel] ?? '0';
    }

    function log($message)
    {
        Log::channel('sync4g')->info("[Service4G] $message");
    }
}
