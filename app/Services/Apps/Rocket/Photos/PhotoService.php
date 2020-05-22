<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 21/05/2020
 * Time: 05:08 PM
 */

namespace App\Services\Apps\Rocket\Photos;

use App\Events\App\Rocket\AppEvent;
use App\Events\App\Rocket\PhotoMapEvent;
use App\Models\Apps\Rocket\CurrentPhoto;
use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use File;
use Image;
use Storage;
use Validator;

class PhotoService
{
    public const PATH = '/Apps/Rocket/Photos/';

    /**
     * @param Vehicle $vehicle
     * @param $data
     * @return object
     */
    public function saveImageData(Vehicle $vehicle, $data)
    {
        $data = collect($data);
        $success = false;
        $message = "";
        $photo = null;

        $validator = Validator::make($data->toArray(), [
            'date' => 'required',
            'img' => 'required',
            'type' => 'required',
            'side' => 'required',
        ]);

        if ($validator->passes()) {
            $photo = new Photo($data->toArray());
            $photo->date = Carbon::createFromFormat('Y-m-d H:i:s', $data->get('date'), 'America/Bogota');
            $photo->vehicle()->associate($vehicle);

            $currentLocation = $vehicle->currentLocation;
            $photo->dispatch_register_id = $currentLocation->dispatch_register_id;
            $photo->location_id = $currentLocation->location_id;

            $imageData = $data->get('img');
            $image = $this->decodeImageData($imageData);
            $imageName = $photo->date->format('YmdHis') . ".jpeg";
            $path = self::PATH . $vehicle->id . "/$imageName";

            $photo->path = $path;
            Storage::disk('local')->put($photo->path, $image);

            if ($photo->save()) {
                $currentPhoto = CurrentPhoto::findByVehicle($vehicle);
                $currentPhoto->fill($data->toArray());
                $currentPhoto->date = $photo->date;
                $currentPhoto->dispatch_register_id = $photo->dispatch_register_id;
                $currentPhoto->location_id = $photo->location_id;
                $currentPhoto->path = $path;

                $success = $currentPhoto->save();
                if ($success) $message = "Photo saved successfully";

                $this->notifyNewPhoto($vehicle);
                $this->notifyHistoric($vehicle);
            }
        } else {
            $message = collect($validator->errors())->flatten()->implode(' ');
        }

        return (object)[
            'response' => [
                'success' => $success,
                'message' => $message,
            ],
            'photo' => $photo
        ];
    }

    /**
     * Request photo via Websockets to Rocket app
     *
     * @param Vehicle $vehicle
     * @param $side
     * @param $quality
     * @return object
     */
    public function takePhoto(Vehicle $vehicle, $side, $quality)
    {
        $params = collect([]);
        $success = false;

        $action = "get-photo";
        if ($action) {
            if (collect(['rear', 'front'])->contains($side)) {
                $options = collect([
                    'action' => "get-photo",
                    'side' => $side,
                    'quality' => $quality
                ]);
                event(new AppEvent($vehicle, $options->toArray()));
                $success = true;
                $message = "Photo has been requested to vehicle $vehicle->number";

                $params = $options;
                $params->put('date', Carbon::now()->toDateTimeString());
            } else {
                $message = "Camera side is invalid";
            }
        } else {
            $message = "Action not found";
        }

        return (object)[
            'success' => $success,
            'message' => $message,
            'params' => (object)$params->toArray()
        ];
    }

    /**
     * @param Vehicle $vehicle
     * @return object
     */
    public function notifyNewPhoto(Vehicle $vehicle)
    {
        $currentPhoto = CurrentPhoto::findByVehicle($vehicle);

        $data = [
            'type' => 'new',
            'url' => $currentPhoto ? $currentPhoto->encode('data-url') : "",
            'vehicle' => $vehicle->getAPIFields(),
            'details' => $currentPhoto->getAPIFields()
        ];

        event(new PhotoMapEvent($vehicle, $data));

        return (object)[
            'success' => true,
            'message' => "Notify for new photo send. Vehicle $vehicle->number"
        ];
    }

    /**
     * @param Vehicle $vehicle
     * @return object
     */
    public function notifyHistoric(Vehicle $vehicle)
    {
        $historic = collect([]);
        $photos = Photo::where('vehicle_id', $vehicle->id)->orderByDesc('date')->get();

        if($photos->isNotEmpty()){
            $prev = $photos->first();
            foreach ($photos as $photo) {
//                $photo->date = Carbon::now();
                if (($prev->id == $photo-> id) || $photo->date->diffInSeconds($prev->date) > 20 ) {
                    $historic->push([
                        'url' => $photo->encode('url'),
                        'details' => $photo->getAPIFields(),
                        'size' => Storage::size($photo->path)
                    ]);
                }
                $prev = $photo;
            }
        }

        $data = [
            'type' => 'historic',
            'vehicle' => $vehicle->getAPIFields(),
            'historic' => $historic->toArray()
        ];

        event(new PhotoMapEvent($vehicle, $data));

        return (object)[
            'success' => true,
            'message' => "Notify for historic photo send. Vehicle $vehicle->number"
        ];
    }

    /**
     * @param Vehicle $vehicle
     * @param string $encode
     * @return \Intervention\Image\Image
     */
    public function getLastPhoto(Vehicle $vehicle, $encode = "webp")
    {
        $currentPhoto = CurrentPhoto::findByVehicle($vehicle);
        if ($vehicle && Storage::exists($currentPhoto->path)) {
            return Image::make(Storage::get($currentPhoto->path))->encode($encode);
        } else {
            return Image::make(File::get('img/image-404.jpg'))->resize(300, 300)->encode($encode);
        }
    }

    /**
     * @param Photo $photo
     * @param string $encode
     * @return \Intervention\Image\Image
     */
    public function getPhoto(Photo $photo, $encode = "webp")
    {
        if (Storage::exists($photo->path)) {
            return Image::make(Storage::get($photo->path))->encode($encode);
        } else {
            return Image::make(File::get('img/image-404.jpg'))->resize(300, 300)->encode($encode);
        }
    }

    /**
     * @param $base64
     * @return false|string
     */
    private function decodeImageData($base64)
    {
        $image_parts = explode(";base64,", $base64);
        $image_type_aux = explode("image/", $image_parts[0]);
        return base64_decode($image_parts[1]);
    }
}
