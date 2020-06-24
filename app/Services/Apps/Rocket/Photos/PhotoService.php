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
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Models\Vehicles\Vehicle;
use App\PerfilSeat;
use Carbon\Carbon;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Storage;
use Validator;

class PhotoService
{
    public $storage;
    public const DISK = 's3';

    public function __construct()
    {
        $this->storage = Storage::disk(self::DISK);
    }

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
            $photo->disk = self::DISK;
            $photo->date = Carbon::createFromFormat('Y-m-d H:i:s', $data->get('date'), 'America/Bogota');
            $photo->vehicle()->associate($vehicle);

            $currentLocation = $vehicle->currentLocation;
            $photo->dispatch_register_id = $currentLocation->dispatch_register_id;
            $photo->location_id = $currentLocation->location_id;

            $image = $this->decodeImageData($data->get('img'));

            $this->storage->put($photo->path, $image);

            if ($photo->save()) {
                $currentPhoto = CurrentPhoto::findByVehicle($vehicle);
                $currentPhoto->fill($data->toArray());
                $currentPhoto->disk = $photo->disk;
                $currentPhoto->date = $photo->date;
                $currentPhoto->data = $photo->data;
                $currentPhoto->persons = $photo->persons;
                $currentPhoto->dispatch_register_id = $photo->dispatch_register_id;
                $currentPhoto->location_id = $photo->location_id;
                $currentPhoto->path = $photo->path;

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

                $this->notifyToMap($vehicle);

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
     * @param null $date
     * @return object
     */
    public function notifyToMap(Vehicle $vehicle, $date = null)
    {
        $currentPhoto = CurrentPhoto::findByVehicle($vehicle);
        $historic = $this->getHistoric($vehicle, $date);

        event(new PhotoMapEvent($vehicle,
            [
                'type' => 'historic',
                'vehicle' => $vehicle->getAPIFields(),
                'historic' => $historic->sortByDesc('time')->values()->toArray(),
            ]
        ));

        $photo = $this->getPhotoData($currentPhoto, $historic);

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
     * @param $photo
     * @param Collection $historic
     * @return object|null
     */
    function getPhotoData(PhotoInterface $photo, $historic)
    {
        $historic = $historic->where('id', '<=', $photo->id);

        $photoData = null;

        if ($photo) {
            $details = $photo->getAPIFields('data-url');
            $details->occupation = $this->processOccupation($photo);

            $personsByRoundTrips = collect([]);
            $historicByTurns = $historic->groupBy('dr');
            foreach ($historicByTurns as $dr => $historicByTurn) {
                $firstHistoricByTurn = $historicByTurn->sortBy('time')->first();
                $lastHistoricByTurn = $historicByTurn->sortBy('time')->last();
                $dispatchRegister = $lastHistoricByTurn->details->dispatchRegister;
                if ($dispatchRegister) {
                    $personsByRoundTrips->push((object)[
                        'id' => $dr,
                        'number' => $dispatchRegister->round_trip,
                        'route' => $dispatchRegister->route->name,
                        'count' => $lastHistoricByTurn->passengers->totalInRoundTrip,

                        'prevCount' => $firstHistoricByTurn->passengers->totalInRoundTrip,
                        'currentCount' => $lastHistoricByTurn->passengers->totalInRoundTrip,
                        'newPersons' => intval($lastHistoricByTurn->passengers->totalInRoundTrip) - intval($firstHistoricByTurn->passengers->totalInRoundTrip),
                    ]);
                }
            }

            $photoData = (object)[
                'id' => $photo->id,
                'details' => $details,
                'passengers' => (object)[
                    'byRoundTrips' => $personsByRoundTrips,
                    'total' => $personsByRoundTrips ? $personsByRoundTrips->sum('count') : 0,
                ],
            ];
        }

        return $photoData;
    }

    /**
     * @param PhotoInterface $currentPhoto
     * @param PhotoInterface $prevPhoto
     * @param bool $firstPhotoInRoundTrip
     * @return int
     */
    public function countOccupation(PhotoInterface $currentPhoto, PhotoInterface $prevPhoto, $firstPhotoInRoundTrip = false)
    {
        $details = $currentPhoto->getAPIFields('url');
        $details->occupation = $this->processOccupation($currentPhoto);

        $currentSeatingOccupied = collect($details->occupation->seatingOccupied);

        $details = $prevPhoto->getAPIFields('url');
        $details->occupation = $this->processOccupation($prevPhoto);

        $prevSeatingOccupied = collect($details->occupation->seatingOccupied);

        $count = 0;
        if ($firstPhotoInRoundTrip) {
            $count = collect($prevSeatingOccupied)->count();
        } else {
            foreach ($prevSeatingOccupied as $seatNumber => $prevSeatOccupied) {
                if (!$currentSeatingOccupied->get($seatNumber)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * @param Vehicle $vehicle
     * @param null $date
     * @return Collection
     */
    public function getHistoric(Vehicle $vehicle, $date = null)
    {
        $historic = collect([]);

        $photos = Photo::findAllByVehicleAndDate($vehicle, $date ? $date : Carbon::now());

        if ($photos->isNotEmpty()) {
            $prev = $photos->first();

            $personsByRoundTrip = 0;
            $totalPersons = 0;

            foreach ($photos as $photo) {

                $details = $photo->getAPIFields('url');
                $details->occupation = $this->processOccupation($photo);

                $dr = $photo->dispatchRegister;
                $currentCount = $details->occupation ? $details->occupation->count : 0;

                $prevOccupation = $this->processOccupation($prev);
                $prevCount = $prevOccupation ? $prevOccupation->count : 0;

                if ($photo->persons === null) {
                    $photo->persons = $currentCount;
                    $photo->save();
                }

                $roundTrip = null;
                $routeName = null;

                $firstPhotoInRoundTrip = $photo->dispatch_register_id != $prev->dispatch_register_id || $prev->id == $photo->id;

                $newPersons = $this->countOccupation($photo, $prev, $firstPhotoInRoundTrip);

                if ($firstPhotoInRoundTrip) {
                    $personsByRoundTrip = $currentCount;
                } else if ($dr) {
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
                    'count' => $personsByRoundTrip,

                    'prevCount' => $prevCount,
                    'currentCount' => $currentCount,
                    'newPersons' => $newPersons,
                    'prevId' => $prev->id,
                ]);

                $historic->push((object)[
                    'id' => $photo->id,
                    'time' => $photo->date->format('H:i:s') . '' . $photo->id,
                    'dr' => $photo->dispatch_register_id,
                    'details' => $details,
                    'passengers' => (object)[
                        'byRoundTrips' => $personsByRoundTrips,
                        'totalInRoundTrip' => $personsByRoundTrip,
                        'total' => $totalPersons,
                    ]
                ]);

                $prev = $photo;
            }
        }

        return $historic;
    }

    /**
     * @param PhotoInterface $photo
     * @return Collection
     */
    public function processOccupation(PhotoInterface $photo)
    {
        $persons = $photo->data;

        if ($persons) {
            $profileSeating = ProfileSeat::findByVehicle($photo->vehicle);
            $personDraws = collect([]);
            $seatingOccupied = collect([]);
            foreach ($persons->draws as $recognition) {
                $recognition = (object)$recognition;
                if ($recognition->count) {
                    $zoneDetected = new PhotoZone($recognition->box);
                    $profileOccupation = $zoneDetected->getProfileOccupation($profileSeating);
                    $recognition->profile = $profileOccupation;

                    if ($profileOccupation->seatOccupied) {
                        $seatingOccupied->put($profileOccupation->seatOccupied->number, $profileOccupation->seatOccupied);
                    }

                    $recognition->profileStr = $profileOccupation->seating->pluck('number')->implode(', ');
                }
                $personDraws[] = $recognition;
            }

            $persons->draws = $personDraws;
            $persons->count = $seatingOccupied->count();
            $persons->seatingOccupied = $seatingOccupied;

            return $persons;
        }

        return null;
    }

    /**
     * @param Vehicle $vehicle
     * @param string $encode
     * @return Image
     * @throws FileNotFoundException
     */
    public function getLastPhoto(Vehicle $vehicle, $encode = "webp")
    {
        $currentPhoto = CurrentPhoto::findByVehicle($vehicle);
        if ($currentPhoto) {
            return $currentPhoto->getImage($encode);
        } else {
            return $this->notFoundImage();
        }
    }

    /**
     * @param Photo $photo
     * @param string $encode
     * @param bool $withEffect
     * @return Image|mixed
     */
    public function getFile(Photo $photo, $encode = "webp", $withEffect = false)
    {
        $image = $photo->getImage($encode, $withEffect);

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

    /**
     * @return mixed
     * @throws FileNotFoundException
     */
    public function notFoundImage()
    {
        return (new Image)->make(File::get('img/image-404.jpg'))->resize(300, 300)->response();
    }
}
