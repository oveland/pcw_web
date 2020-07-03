<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 21/05/2020
 * Time: 05:08 PM
 */

namespace App\Services\Apps\Rocket\Photos;

use App;
use App\Events\App\Rocket\AppEvent;
use App\Events\App\Rocket\PhotoMapEvent;
use App\Models\Apps\Rocket\CurrentPhoto;
use App\Models\Apps\Rocket\Photo;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Models\Vehicles\Vehicle;
use App\PerfilSeat;
use Carbon\Carbon;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Image;
use Storage;
use Validator;

class PhotoService
{
    public const DISK = 's3';
    public const REKOGNITION_TYPE = 'persons_and_faces';
//    public const REKOGNITION_TYPE = 'persons';
//    public const REKOGNITION_TYPE = 'faces';

    /**
     * @var Filesystem
     */
    public $storage;

    /**
     * @var Vehicle
     */
    public $vehicle;


    function __construct()
    {
        $this->storage = Storage::disk(self::DISK);
    }

    /**
     * @param Vehicle $vehicle
     * @return PhotoService
     */
    function for(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @param $data
     * @return object
     */
    function saveImageData($data)
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
            $photo->vehicle()->associate($this->vehicle);

            $currentLocation = $this->vehicle->currentLocation;
            $photo->dispatch_register_id = $currentLocation->dispatch_register_id;
            $photo->location_id = $currentLocation->location_id;

            $image = $this->decodeImageData($data->get('img'));

            $this->storage->put($photo->path, $image);

            if ($photo->save()) {
                $currentPhoto = CurrentPhoto::findByVehicle($this->vehicle);
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

                $this->notifyToMap();
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
     * @param null $date
     * @return object
     */
    function notifyToMap($date = null)
    {
        $currentPhoto = CurrentPhoto::findByVehicle($this->vehicle);
        $historic = $this->getHistoric($date);

        event(new PhotoMapEvent($this->vehicle,
            [
                'type' => 'historic',
                'vehicle' => $this->vehicle->getAPIFields(),
                'historic' => $historic->sortByDesc('time')->values()->toArray(),
            ]
        ));

        $photo = $this->getPhotoData($currentPhoto, $historic);

        event(new PhotoMapEvent($this->vehicle,
            [
                'type' => 'new',
                'vehicle' => $this->vehicle->getAPIFields(),
                'photo' => $photo
            ]
        ));

        return (object)[
            'success' => true,
            'message' => "Notify to map send. Vehicle " . $this->vehicle->number
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
            $details->occupation = $this->getOccupation($photo);

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
     * @param $currentOccupation
     * @param $prevOccupation
     * @param bool $firstPhotoInRoundTrip
     * @return int
     */
    private function countOccupation($currentOccupation, $prevOccupation, $firstPhotoInRoundTrip = false)
    {
        $count = 0;
        if ($currentOccupation && $prevOccupation) {
            $currentSeatingOccupied = collect($currentOccupation->seatingOccupied);
            $prevSeatingOccupied = collect($prevOccupation->seatingOccupied);

            if ($prevOccupation->count && $currentOccupation->count) {
                if ($firstPhotoInRoundTrip) {
                    $count = collect($prevSeatingOccupied)->count();
                } else {
                    foreach ($prevSeatingOccupied as $seatNumber => $prevSeatOccupied) {
                        if (!$currentSeatingOccupied->get($seatNumber)) {
                            $count++;
                        }
                    }
                }
            }
        }

        return $count;
    }

    private function getOccupation(PhotoInterface $photo, $type = self::REKOGNITION_TYPE)
    {

        switch ($type){
            case 'persons':
            case 'faces':
                return $this->getRekognitionService($type)->processOccupation($photo);
                break;
            case 'persons_and_faces':
                $personsOccupation = $this->getRekognitionService('persons')->processOccupation($photo);
                $facesOccupation = $this->getRekognitionService('faces')->processOccupation($photo);

                $occupation = $personsOccupation;

                if($facesOccupation){
                    foreach ($facesOccupation->seatingOccupied as $seatNumber => $seatOccupied){
                        $occupation->seatingOccupied->put($seatNumber, $seatOccupied);
                    }

                    $occupation->persons = $occupation->seatingOccupied->count();
                    $occupation->draws = $occupation->draws->merge($facesOccupation->draws);
                }


                $occupation->seatingOccupiedStr = $occupation->seatingOccupied->keys()->sort()->implode(', ');

                return $occupation;

                break;
        }

        return null;
    }

    /**
     * @param null $date
     * @return Collection
     */
    function getHistoric($date = null)
    {
        $historic = collect([]);

        $photos = Photo::findAllByVehicleAndDate($this->vehicle, $date ? $date : Carbon::now());

        if ($photos->isNotEmpty()) {
            $prevPhoto = $photos->first();

            $personsByRoundTrip = 0;
            $totalPersons = 0;

            foreach ($photos as $photo) {

                $currentOccupation = $this->getOccupation($photo);
                $prevOccupation = $this->getOccupation($prevPhoto);

                $currentCount = $currentOccupation ? $currentOccupation->persons : 0;
                $prevCount = $prevOccupation ? $prevOccupation->persons : 0;

                if ($photo->persons === null) {
                    $photo->persons = $currentCount;
                    $photo->save();
                }

                $roundTrip = null;
                $routeName = null;

                $dr = $photo->dispatchRegister;
                $firstPhotoInRoundTrip = $photo->dispatch_register_id != $prevPhoto->dispatch_register_id || $prevPhoto->id == $photo->id;

                $newPersons = $this->countOccupation($currentOccupation, $prevOccupation, $firstPhotoInRoundTrip);

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
                    'persons' => $personsByRoundTrip,

                    'prevCount' => $prevCount,
                    'currentCount' => $currentCount,
                    'newPersons' => $newPersons,
                    'prevId' => $prevPhoto->id,
                ]);


                $details = $photo->getAPIFields('url');
                $details->occupation = $currentOccupation;

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

                if ($details->occupation->count) $prevPhoto = $photo;
            }
        }

        return $historic;
    }


    /**
     * @param string $encode
     * @return \Intervention\Image\Image
     */
    function getLastPhoto($encode = "webp")
    {
        $currentPhoto = CurrentPhoto::findByVehicle($this->vehicle);
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
    function getFile(Photo $photo, $encode = "webp", $withEffect = false)
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
     */
    function notFoundImage()
    {
        try {
            return (new Image)->make(File::get('img/image-404.jpg'))->resize(300, 300)->response();
        } catch (FileNotFoundException $e) {
            return null;
        }
    }

    /**
     * @param string $type
     * @return PhotoRekognitionService
     */
    function getRekognitionService($type = self::REKOGNITION_TYPE)
    {
        return App::make("rocket.photo.rekognition.$type", ['vehicle' => $this->vehicle]);
    }
}
