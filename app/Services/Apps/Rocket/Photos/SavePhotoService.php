<?php
namespace App\Services\Apps\Rocket\Photos;
use App\Models\Apps\Rocket\CurrentPhoto;
use App\Models\Apps\Rocket\Photo;
use App\Services\Apps\Rocket\Photos\PhotoService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use DB;
use App\Models\Vehicles\CurrentLocation;
use Carbon\Carbon;

class SavePhotoService extends PhotoService
{
    function saveImageData($data, $withPhoto = false)
    {
        $data = collect($data);
        $success = false;
        $message = "";
        $photo = null;
        $path = null;
        $pathUploaded = false;
        $photoSaved = false;
        $uid = $data['uid'];
        $context = [
            'uid' => $uid,
            'vehicle_id' => optional($this->vehicle)->id,
            'side' => $data->get('side'),
            'type' => $data->get('type'),
            'file_name' => $data->get('file_name'),
            'file_type' => $data->get('file_type'),
        ];

        $validator = Validator::make($data->toArray(), [
            'date' => 'required',
            'img' => 'required',
            'type' => 'required',
            'side' => 'required',
            'uid' => 'required|unique:app_photos'
        ]);

        if ($validator->passes()) {
            try {
                $photo = new Photo($data->toArray());
                $photo->disk = self::DISK;
                $photo->date = Carbon::createFromFormat('Y-m-d H:i:s', $data->get('date'), 'America/Bogota');
                $photo->vehicle()->associate($this->vehicle);

                $currentLocation = $this->vehicle->currentLocation;
                $dr = $this->findDispatchRegisterByPhoto($photo, $currentLocation);
                $photo->dispatch_register_id = $dr ? $dr->id : null;

                $photo->location_id = $currentLocation->location_id ?? null;
                $vId = $this->vehicle->id;
                $initialDate = $photo->date->toDateString();
                $finalDate = $photo->date->toDateTimeString();
                $location = collect(DB::select("SELECT id from locations WHERE vehicle_id = $vId and date between '$initialDate' AND '$finalDate' ORDER BY date DESC LIMIT 1"))->first();
                $photo->location_id = $location ? $location->id : null;

                $path = $photo->path;
                $image = $this->decodeImageData($data->get('img'));

                $this->safeRocketLog('info', "SavePhotoService: starting upload", $context + [
                    'path' => $path,
                    'dispatch_register_id' => $photo->dispatch_register_id,
                    'location_id' => $photo->location_id,
                ]);

                $storageResponse = $this->storage->put($path, $image);
                $pathUploaded = (bool)$storageResponse;
                if (!$storageResponse) {
                    $message = "Image $uid has invalid format!";
                    $this->safeRocketLog('error', "SavePhotoService: S3 upload returned false", $context + ['path' => $path]);
                } else {
                    try {
                        DB::beginTransaction();

                        if (!$photo->save()) {
                            throw new \RuntimeException("Error saving data $uid");
                        }
                        $photoSaved = true;

                        $currentPhoto = CurrentPhoto::findByVehicle($this->vehicle);
                        $currentPhoto->fill($data->toArray());
                        $currentPhoto->disk = $photo->disk;
                        $currentPhoto->date = $photo->date;
                        $currentPhoto->data = $photo->data;
                        $currentPhoto->persons = $photo->persons;
                        $currentPhoto->dispatch_register_id = $photo->dispatch_register_id;
                        $currentPhoto->location_id = $photo->location_id;
                        $currentPhoto->path = $path;

                        if (!$currentPhoto->save()) {
                            throw new \RuntimeException("Current photo update failed for $uid");
                        }

                        DB::commit();
                    } catch (\Throwable $dbException) {
                        if (DB::transactionLevel() > 0) {
                            DB::rollBack();
                        }
                        $photoSaved = false;
                        throw $dbException;
                    }

                    $success = true;
                    $message = "Photo $uid saved successfully";

                    $this->safeRocketLog('info', "SavePhotoService: photo saved successfully", $context + [
                        'path' => $path,
                        'photo_id' => $photo->id,
                        'dispatch_register_id' => $photo->dispatch_register_id,
                    ]);
                }
            } catch (\Throwable $e) {
                $path = $path ?: ($photo ? $photo->getOriginalPath() : null);

                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                if ($photoSaved && $photo && $photo->exists) {
                    try {
                        $photo->delete();
                    } catch (\Throwable $deletePhotoException) {
                        $this->safeRocketLog('error', "SavePhotoService: failed to rollback photo row after exception", $context + [
                            'path' => $path,
                            'rollback_error' => $deletePhotoException->getMessage(),
                        ]);
                    }
                }

                if ($pathUploaded && $path) {
                    try {
                        $this->storage->delete($path);
                    } catch (\Throwable $deleteException) {
                        $this->safeRocketLog('error', "SavePhotoService: failed to rollback S3 file after exception", $context + [
                            'path' => $path,
                            'rollback_error' => $deleteException->getMessage(),
                        ]);
                    }
                }

                $message = "Error saving file $uid: " . $e->getMessage();
                $this->safeRocketLog('error', "SavePhotoService: exception while saving photo", $context + [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            $photoSaved = Photo::where('uid', $uid)->first();

            if ($photoSaved) {
                $photoExistsInDisk = false;

                try {
                    $photoExistsInDisk = $photoSaved->disk && $photoSaved->path
                        ? $this->storageDriverFor($photoSaved->disk)->exists($photoSaved->path)
                        : false;
                } catch (\Throwable $e) {
                    $photoExistsInDisk = false;
                }

                if ($photoExistsInDisk) {
                    $success = true;
                    $message = "Photo $uid is already saved";
                    $this->safeRocketLog('info', "SavePhotoService: duplicated uid already saved", $context + [
                        'photo_id' => $photoSaved->id,
                    ]);
                } else {
                    $success = false;
                    $message = "Photo $uid exists in database but file is missing in storage";
                    $this->safeRocketLog('warning', "SavePhotoService: stale photo row detected", $context + [
                        'photo_id' => $photoSaved->id,
                        'disk' => $photoSaved->disk,
                        'path' => $photoSaved->path,
                    ]);
                }
            } else {
                $success = false;
                $message = "Error saving photo $uid: " . collect($validator->errors())->flatten()->implode(' ');
                $this->safeRocketLog('warning', "SavePhotoService: validation failed", $context + [
                    'errors' => collect($validator->errors())->flatten()->implode(' '),
                ]);
            }
        }

        return (object)[
            'response' => (object)[
                'success' => $success,
                'message' => $message,
            ],
            'photo' => $success && $withPhoto ? $photo->getAPIFields() : null
        ];
    }

    private function safeRocketLog(string $level, string $message, array $context = []): void
    {
        try {
            Log::channel('rocket')->$level($message, $context);
        } catch (\Throwable $e) {
            // El log nunca debe romper el guardado de la foto.
        }
    }

    private function storageDriverFor(string $disk)
    {
        return \Storage::disk($disk);
    }
}
