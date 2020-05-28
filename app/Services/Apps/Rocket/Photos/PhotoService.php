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
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
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
     * @throws FileNotFoundException
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
            $photo->dispatch_register_id = 1183603; //$currentLocation->dispatch_register_id;
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
                $currentPhoto->data = $photo->data;
                $currentPhoto->dispatch_register_id = $photo->dispatch_register_id;
                $currentPhoto->location_id = $photo->location_id;
                $currentPhoto->path = $path;

                $success = $currentPhoto->save();
                if ($success) $message = "Photo saved successfully";

                $this->notifyToMap($vehicle);
            }
        } else {
            $message = collect($validator->errors())->flatten()->implode(' ');
        }

        return (object)[
            'response' => (object)[
                'success' => $success,
                'message' => $message,
            ],
            'photo' => $success ? $photo->getAPIFields() : null
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
     * @throws FileNotFoundException
     */
    public function notifyToMap(Vehicle $vehicle)
    {
        $currentPhoto = CurrentPhoto::findByVehicle($vehicle);
        $historic = $this->getHistoric($vehicle);

        event(new PhotoMapEvent($vehicle,
            [
                'type' => 'historic',
                'vehicle' => $vehicle->getAPIFields(),
                'historic' => $historic->sortByDesc('id')->values()->toArray(),
            ]
        ));

        $photo = null;
        if ($currentPhoto) {
            $currentPhoto = $currentPhoto->getAPIFields('data-url');

            $personsByRoundTrips = collect([]);
            $historicByTurns = $historic->groupBy('dr');
            foreach ($historicByTurns as $dr => $historicByTurn) {
                $lastHistoricByTurn = $historicByTurn->last();
                $dispatchRegister = $lastHistoricByTurn->details->dispatchRegister;
                if ($dispatchRegister) {
                    $personsByRoundTrips->push((object)[
                        'id' => $dr,
                        'number' => $dispatchRegister->round_trip,
                        'route' => $dispatchRegister->route->name,
                        'count' => $lastHistoricByTurn->passengers->totalInRoundTrip
                    ]);
                }
            }

            $photo = (object)[
                'id' => $currentPhoto->id,
                'details' => $currentPhoto,
                'passengers' => (object)[
                    'byRoundTrips' => $personsByRoundTrips,
                    'total' => $personsByRoundTrips ? $personsByRoundTrips->sum('count') : 0,
                ],
            ];
        }

        event(new PhotoMapEvent($vehicle,
            [
                'type' => 'new',
                'vehicle' => $vehicle->getAPIFields(),
                'photo' => $photo
            ]
        ));

        return (object)[
            'success' => true,
            'message' => "Notify to map send. Vehicle $vehicle->number"
        ];
    }


    /**
     * @param Vehicle $vehicle
     * @param null $date
     * @return Collection
     * @throws FileNotFoundException
     */
    public function getHistoric(Vehicle $vehicle, $date = null)
    {
        $historic = collect([]);
        $photos = Photo::where('vehicle_id', $vehicle->id)->whereDate('date', $date ? $date : Carbon::now())->orderBy('date')->get();

        if ($photos->isNotEmpty()) {
            $prev = $photos->first();

            $personsByRoundTrip = 0;
            $totalPersons = 0;

            foreach ($photos as $photo) {
                if (($prev->id == $photo->id) || $photo->date->diffInSeconds($prev->date) > 20) {
                    $dr = $photo->dispatchRegister;
                    $currentCount = $photo->data ? $photo->data->count : 0;
                    $prevCount = $prev->data ? $prev->data->count : 0;
                    $difference = $currentCount - $prevCount;
                    $newPersons = $difference > 0 ? $difference : 0;

                    $roundTrip = null;
                    $routeName = null;

                    $firstPhotoInRoundTrip = $photo->dispatch_register_id != $prev->dispatch_register_id || $prev->id == $photo->id;

                    if ($firstPhotoInRoundTrip) {
                        $personsByRoundTrip = $currentCount;
                    } else {
                        $personsByRoundTrip += $newPersons;
                    }

                    if ($dr) {
                        $roundTrip = $dr->round_trip;
                        $routeName = $dr->route->name;
                        $totalPersons += $firstPhotoInRoundTrip ? $currentCount : $newPersons;
                    }

                    $personsByRoundTrips = collect([])->push((object)[
                        'id' => $dr ? $dr->id : null,
                        'number' => $roundTrip,
                        'route' => $routeName,
                        'count' => $personsByRoundTrip
                    ]);

                    $historic->push((object)[
                        'id' => $photo->id,
                        'dr' => $photo->dispatch_register_id,
                        'details' => $photo->getAPIFields('url'),
                        'passengers' => (object)[
                            'byRoundTrips' => $personsByRoundTrips,
                            'totalInRoundTrip' => $personsByRoundTrip,
                            'total' => $totalPersons,
                        ]
                    ]);
                }
                $prev = $photo;
            }
        }

        return $historic;
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
            $image = Image::make(Storage::get($photo->path));
        } else {
            $image = Image::make(File::get('img/image-404.jpg'))->resize(300, 300);
        }

        if (collect(['png', 'jpg', 'jpeg', 'gif'])->contains($encode)) {
            return $image->response($encode);
        }

        return $image->encode($encode);
    }

    /**
     * @param $base64
     * @return false|string
     */
    private function decodeImageData($base64)
    {
        $image_parts = explode(";base64,", $base64);
        return base64_decode($image_parts[1]);
    }
}
