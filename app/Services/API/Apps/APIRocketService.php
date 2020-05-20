<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\Models\Apps\Rocket\Battery;
use App\Models\Apps\Rocket\CurrentBattery;
use App\Models\Apps\Rocket\CurrentPhoto;
use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\CurrentLocation;
use App\Models\Vehicles\Vehicle;
use App\Services\API\Apps\Contracts\APIAppsInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;
use Validator;

class APIRocketService implements APIAppsInterface
{
    /**
     * @var Request
     */
    private $request;
    private $service;

    /**
     * @var Vehicle
     */
    private $vehicle;

    private $currentLocation;

    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');
        $this->vehicle = Vehicle::where('plate', $this->request->get('vehicle'))->first();
        if ($this->vehicle) $this->currentLocation = CurrentLocation::findByVehicle($this->vehicle);
    }

    /**
     * @return JsonResponse
     */
    public function serve(): JsonResponse
    {
        if ($this->vehicle) {
            switch ($this->service) {
                case 'save-photo':
                    return $this->savePhoto();
                    break;

                case 'save-battery':
                    return $this->saveBattery();
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => __('Service not found')
                    ]);
                    break;
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Vehicle not found'
        ]);
    }

    public function savePhoto()
    {
        $success = false;
        $message = "";

        $validator = Validator::make($this->request->all(), [
            'date' => 'required',
            'img' => 'required',
            'type' => 'required',
            'side' => 'required',
        ]);

        if ($validator->passes()) {
            $photo = new Photo();
            $photo->fill($this->request->all());
            $photo->vehicle()->associate($this->vehicle);

            $currentLocation = $this->vehicle->currentLocation;
            $photo->dispatch_register_id = $currentLocation->dispatch_register_id;
            $photo->location_id = $currentLocation->location_id;

            $imageData = $this->request->get('img');
            $image = $this->decodeImageData($imageData);
            $imageName = $photo->date->format('YmdHis') . ".jpeg";
            $path = "/Apps/Rocket/Photo/" . $this->vehicle->id . "/$imageName";

            $photo->path = $path;
            Storage::disk('local')->put($photo->path, $image);

            Storage::disk('local')->append('photo.log', "$imageData\n$photo->date\n$photo->side: $photo->type\n" . $this->vehicle->plate . ":\n");

            if ($photo->save()) {
                $currentPhoto = CurrentPhoto::findByVehicle($this->vehicle);
                $currentPhoto->fill($photo->toArray());
                $success = $currentPhoto->save();
                if ($success) $message = "Photo saved successfully";
            }
        } else {
            $message = collect($validator->errors())->flatten()->implode(' ');
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    private function decodeImageData($base64)
    {
        $image_parts = explode(";base64,", $base64);
        $image_type_aux = explode("image/", $image_parts[0]);
        return base64_decode($image_parts[1]);
    }

    public
    function saveBattery()
    {
        $success = false;
        $message = "";

        $validator = Validator::make($this->request->all(), [
            'date' => 'required',
            'img' => 'required',
            'type' => 'required',
            'side' => 'required',
        ]);

        if ($validator->passes()) {
            $battery = new Battery();
            $battery->fill($this->request->all());
            $battery->vehicle()->associate($this->vehicle);
            $battery->date_changed = $this->request->get('dateChanged');

            Storage::disk('local')->append('battery.log', $this->vehicle->plate . ": $battery->level%, charging: $battery->charging, $battery->date_changed, $battery->date");

            if ($battery->save()) {
                $currentBattery = CurrentBattery::findByVehicle($this->vehicle);
                $currentBattery->fill($battery->toArray());
                $success = $currentBattery->save();
                if ($success) $message = "Photo saved successfully";
            }
        } else {
            $message = collect($validator->errors())->flatten()->implode(' ');
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }
}