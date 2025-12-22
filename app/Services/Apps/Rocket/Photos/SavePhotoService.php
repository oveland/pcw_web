<?php
namespace App\Services\Apps\Rocket\Photos;
use App\Models\Apps\Rocket\CurrentPhoto;
use App\Models\Apps\Rocket\Photo;
use App\Services\Apps\Rocket\Photos\PhotoService;
use Illuminate\Support\Facades\Validator;
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

        $validator = Validator::make($data->toArray(), [
            'date' => 'required',
            'img' => 'required',
            'type' => 'required',
            'side' => 'required',
            'uid' => 'required|unique:app_photos'
        ]);
        //dd($validator->errors(), $validator->passes(), $validator->validated(), $data);

        if ($validator->passes()) {

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

            $image = $this->decodeImageData($data->get('img'));


            try {
                $storageResponse = $this->storage->put($photo->path, $image);

                if ($photo->save() && $storageResponse) {
                    $currentPhoto = CurrentPhoto::findByVehicle($this->vehicle);
                    $currentPhoto->fill($data->toArray());
                    $currentPhoto->disk = $photo->disk;
                    $currentPhoto->date = $photo->date;
                    $currentPhoto->data = $photo->data;
                    $currentPhoto->persons = $photo->persons;
                    $currentPhoto->dispatch_register_id = $photo->dispatch_register_id;
                    $currentPhoto->location_id = $photo->location_id;
                    $currentPhoto->path = $photo->path;

                    $currentPhoto->save();
                    $success = true;
                    $message = "Photo $uid saved successfully";
                } else {
                    if (!$storageResponse) $message = "Image $uid has invalid format!";
                    else $message = "Error saving data $uid";
                }

            } catch (Exception $e) {
                throw $e;
                $message = "Error saving file $uid: " . $e;
            }
        } else {

            $photoSaved = Photo::where('uid', $uid)->first();

            if ($uid) {
                $success = true;
                $message = "Photo $uid is currently saved but processRekognition has error...";
            } else {
                $success = false;
                $message = "Error saving photo $uid: " . collect($validator->errors())->flatten()->implode(' ');
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