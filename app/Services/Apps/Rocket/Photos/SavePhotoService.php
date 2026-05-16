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

                Log::channel('rocket')->info("SavePhotoService: starting upload", $context + [
                    'path' => $path,
                    'dispatch_register_id' => $photo->dispatch_register_id,
                    'location_id' => $photo->location_id,
                ]);

                $storageResponse = $this->storage->put($path, $image);
                if (!$storageResponse) {
                    $message = "Image $uid has invalid format!";
                    Log::channel('rocket')->error("SavePhotoService: S3 upload returned false", $context + ['path' => $path]);
                } elseif (!$photo->save()) {
                    $message = "Error saving data $uid";
                    $this->storage->delete($path);
                    Log::channel('rocket')->error("SavePhotoService: photo model save returned false", $context + ['path' => $path]);
                } else {
                    $currentPhoto = CurrentPhoto::findByVehicle($this->vehicle);
                    $currentPhoto->fill($data->toArray());
                    $currentPhoto->disk = $photo->disk;
                    $currentPhoto->date = $photo->date;
                    $currentPhoto->data = $photo->data;
                    $currentPhoto->persons = $photo->persons;
                    $currentPhoto->dispatch_register_id = $photo->dispatch_register_id;
                    $currentPhoto->location_id = $photo->location_id;
                    $currentPhoto->path = $path;

                    $success = true;
                    $message = "Photo $uid saved successfully";

                    if (!$currentPhoto->save()) {
                        $message .= " (warning: current photo was not updated)";
                        Log::channel('rocket')->warning("SavePhotoService: current photo save returned false", $context + [
                            'path' => $path,
                            'photo_id' => $photo->id,
                        ]);
                    }

                    Log::channel('rocket')->info("SavePhotoService: photo saved successfully", $context + [
                        'path' => $path,
                        'photo_id' => $photo->id,
                        'dispatch_register_id' => $photo->dispatch_register_id,
                    ]);
                }
            } catch (\Throwable $e) {
                $path = $photo ? $photo->getOriginalPath() : null;
                if ($path) {
                    try {
                        $this->storage->delete($path);
                    } catch (\Throwable $deleteException) {
                        Log::channel('rocket')->error("SavePhotoService: failed to rollback S3 file after exception", $context + [
                            'path' => $path,
                            'rollback_error' => $deleteException->getMessage(),
                        ]);
                    }
                }

                $message = "Error saving file $uid: " . $e->getMessage();
                Log::channel('rocket')->error("SavePhotoService: exception while saving photo", $context + [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            $photoSaved = Photo::where('uid', $uid)->first();

            if ($photoSaved) {
                $success = true;
                $message = "Photo $uid is already saved";
                Log::channel('rocket')->info("SavePhotoService: duplicated uid already saved", $context + [
                    'photo_id' => $photoSaved->id,
                ]);
            } else {
                $success = false;
                $message = "Error saving photo $uid: " . collect($validator->errors())->flatten()->implode(' ');
                Log::channel('rocket')->warning("SavePhotoService: validation failed", $context + [
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
}
