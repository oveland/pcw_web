<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 21/05/2020
 * Time: 05:08 PM
 */

namespace App\Services\Apps\Rocket\Photos;

use App;
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
        $lastPhoto = $historic->last();

        $photoData = null;

        if ($photo) {
            $details = $photo->getAPIFields('data-url');
            $details->occupation = $this->getOccupation($photo);

            $personsByRoundTrips = collect([]);
            $historicByTurns = $historic->groupBy('dr');
            $lastHistoricByTurn = null;
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
                'prevDetails' => $lastPhoto->prevDetails ?? null,
                'seatingBoardingStr' => $lastPhoto->seatingBoardingStr ?? null,
                'seatingMixStr' => $lastPhoto->seatingMixStr ?? null,
                'seatingReleaseStr' => $lastPhoto->seatingReleaseStr ?? null,
                'seatingActivatedStr' => $lastPhoto->seatingActivatedStr ?? null,
                'alarms' => $lastPhoto->alarms ?? null,
                'passengers' => (object)[
                    'byRoundTrips' => $personsByRoundTrips,
                    'total' => $personsByRoundTrips ? $personsByRoundTrips->sum('count') : 0,
                    'totalSum' => $historic->count() ? $historic->last()->passengers->totalSum : 0,
                    'totalSum2' => $historic->count() ? $historic->last()->passengers->totalSum2 : 0
                ],
            ];
        }

        return $photoData;
    }


    public function processPersistenceSeating(&$currentOccupied, &$prevOccupied)
    {
        $currentOccupiedMod = collect([]);
        foreach ($currentOccupied as $seat => $data) {
            $inPrevSeat = $prevOccupied->get($seat);

            $newData = collect($data);
            $newData->put('counterActivate', $inPrevSeat ? (intval(isset($inPrevSeat->counterActivate) ? $inPrevSeat->counterActivate : 0) + 1) : 0);

            $currentOccupiedMod->put($seat, (object)$newData->toArray());
        }

        $currentOccupied = $currentOccupiedMod;
//
//        $prevOccupiedMod = collect([]);
//        foreach ($prevOccupied as $seat => $data) {
//            $inCurrentSeat = $currentOccupied->get($seat);
//
//            $newData = collect($data);
//            $newData->put('counterRelease', $inCurrentSeat && $inCurrentSeat->counterActivate <= 1 ? 0 : intval(isset($data->counterRelease) ? $data->counterRelease : 0) + 1);
//
//            if ($newData->get('counterRelease') > 1) {
//                $currentOccupied->put($seat, (object)$newData->toArray());
//            }
//
//            $prevOccupiedMod->put($seat, (object)$newData->toArray());
//        }
//
//        $prevOccupied = $prevOccupiedMod;
    }

    /**
     * @param Collection $currentOccupied
     * @param Collection $prevOccupied
     * @return Collection
     */
    public function getSeatingReleased($currentOccupied, $prevOccupied)
    {
        $seatingRelease = collect([]);

        if ($prevOccupied->count() && $prevOccupied->count() || true) {
//            $prevOccupiedMod = collect([]);
            foreach ($prevOccupied as $seat => $data) {
//                $inCurrentSeat = $currentOccupied->get($seat);

                $newData = collect($data);
//                $newData->put('counterRelease', $inCurrentSeat ? 0 : intval(isset($data->counterRelease) ? $data->counterRelease : 0) + 1);

                if ($newData->get('counterRelease') > 1) {
                    $seatingRelease->put($seat, (object)$newData->toArray());
                } else {
//                    $currentOccupied->put($seat, (object)$newData->toArray());
                }

//                $prevOccupiedMod->put($seat, (object)$newData->toArray());
            }
//            $prevOccupied = $prevOccupiedMod;
        }
        return $seatingRelease;
    }

    /**
     * @param Collection $currentOccupied
     * @param Collection $prevOccupied
     * @return Collection
     */
    public function getSeatingActivated($currentOccupied, $prevOccupied)
    {
        $seatingBusy = collect([]);

//        $currentOccupiedMod = collect([]);
        foreach ($currentOccupied as $seat => $data) {
//            $inPrevSeat = $prevOccupied->get($seat);

            $newData = collect($data);
//            $newData->put('counterActivate', $inPrevSeat ? (intval(isset($inPrevSeat->counterActivate) ? $inPrevSeat->counterActivate : 0) + 1) : 0);

            if ($newData->get('counterActivate') == 2) {
//                $newData->put('counterActivate', 0);
                $seatingBusy->put($seat, (object)$newData->toArray());
            }

//            $currentOccupiedMod->put($seat, (object)$newData->toArray());
        }
//        $currentOccupied = $currentOccupiedMod;

        return $seatingBusy;
    }

    /**
     * @param $currentOccupation
     * @param $prevOccupation
     * @return int
     */
    private function countOccupation($currentOccupation, $prevOccupation)
    {
        $count = 0;
        if ($currentOccupation && $prevOccupation) {
            $currentSeatingOccupied = collect($currentOccupation->seatingOccupied);
            $prevSeatingOccupied = collect($prevOccupation->seatingOccupied);

            if ($prevOccupation->count && $currentOccupation->count) {
                foreach ($prevSeatingOccupied as $seatNumber => $prevSeatOccupied) {
                    if (!$currentSeatingOccupied->get($seatNumber)) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    private function getOccupation(PhotoInterface $photo, $type = self::REKOGNITION_TYPE)
    {

        switch ($type) {
            case 'persons':
            case 'faces':
                return $this->getRekognitionService($type)->processOccupation($photo);
                break;
            case 'persons_and_faces':
                $profileSeating = ProfileSeat::findByVehicle($this->vehicle);
                $personsRekognition = $this->getRekognitionService('persons');
                $facesRekognition = $this->getRekognitionService('faces');

                $personsOccupation = $personsRekognition->processOccupation($photo);
                $facesOccupation = $facesRekognition->processOccupation($photo);

                $occupation = $personsOccupation;

                if ($facesOccupation) {
                    $occupation->draws = $occupation->draws->merge($facesOccupation->draws);
                }

                $occupation = $this->filterDrawsInsideOverlap($occupation);
//                $occupation->type = $type;

                return $personsRekognition->occupationParams($profileSeating, $occupation);

                break;
        }

        return null;
    }

    function filterDrawsInsideOverlap($occupation)
    {
        if ($occupation->withOverlap) {

            $nd = collect([]);
            foreach ($occupation->draws as &$rekognition) {
                $rekognition = (object)$rekognition;
                if ($rekognition->count) {
                    $recognitionZone = new Zone($rekognition->box);

                    $partnersDraws = $occupation->draws;

                    foreach ($partnersDraws as $partner) {
                        if ($partner->overlap) {
                            $partnerZone = new Zone($partner->box);

                            $rekognition->isInside = $recognitionZone->isInsideOf($partnerZone);

                            if ($rekognition->isInside) {
                                $rekognition->count = false;
                                $rekognition->color = "rgb(0, 239, 222)";
                            }
                        }
                    }
                }

                $nd->push($rekognition);
            }

            $occupation->draws = $nd->toArray();
        }

        return $occupation;
    }

    /**
     * @param PhotoInterface $currentPhoto
     * @param $counterLock
     * @param $alert
     */
    public function processLockCam(PhotoInterface $currentPhoto, &$counterLock, &$alert)
    {
        $currentAvBrightness = $currentPhoto->data_properties->avBrightness ?? null;

        if ($currentAvBrightness !== null && $currentAvBrightness < 2) {
            $counterLock++;
        } else {
            $counterLock = 0;
        }
        $alert = $counterLock >= 3;
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
            $prevOccupation = $this->getOccupation($prevPhoto);

            $personsByRoundTrip = 0;
            $totalPersons = 0;
            $totalSum = 0;
            $totalSum2 = 0;

            $alerts = collect([]);
            $counterLock = 0;
            $alertLockCam = false;

            foreach ($photos as $photo) {

                $currentOccupation = $this->getOccupation($photo);

                $details = $photo->getAPIFields('url');

                $prevDetails = $prevPhoto->getAPIFields('url');
                $prevDetails->occupation = $prevOccupation;

                $seatingBoarding = clone $currentOccupation->seatingOccupied;

                if ($currentOccupation->withOverlap) {
                    foreach ($prevOccupation->seatingOccupied as $seatNumber => $seatOccupied) {
                        $currentOccupation->seatingOccupied->put($seatNumber, $seatOccupied);
                    }
                }

                $details->occupation = $currentOccupation;


                $this->processPersistenceSeating($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied);

//                $seatingReleased = $this->getSeatingReleased($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied);
                $seatingActivated = $this->getSeatingActivated($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied);


                $seatingReleased = collect([]);

//                $newPersons = $seatingReleased->count();
                $newPersons = $seatingActivated->count();

                $totalSum += $currentOccupation->withOverlap ? 0 : $newPersons;
                $totalSum2 += $seatingActivated->count();

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


                if ($firstPhotoInRoundTrip) {
                    $personsByRoundTrip = $currentOccupation->seatingOccupied->count();
                } else
                    if ($dr) {
                        $personsByRoundTrip += $newPersons;
                    }

                if ($dr) {
                    $roundTrip = $dr->round_trip;
                    $routeName = $dr->route->name;
                    $totalPersons += $firstPhotoInRoundTrip ? $currentOccupation->seatingOccupied->count() : $newPersons;
//                    $totalPersons +=  $newPersons;
                }

                $personsByRoundTrips = collect([])->push((object)[
                    'id' => $dr ? $dr->id : null,
                    'number' => $roundTrip,
                    'route' => $routeName,
                    'persons' => $personsByRoundTrip,
                    'count' => $personsByRoundTrip,

                    'prevCount' => $prevCount,
                    'currentCount' => $currentCount,
                    'newPersons' => $newPersons,
                    'prevId' => $prevPhoto->id,
                ]);

                $this->processLockCam($photo, $counterLock, $alertLockCam);

                $historic->push((object)[
                    'id' => $photo->id,
                    'time' => $photo->date->format('H:i:s') . '' . $photo->id,
                    'dr' => $photo->dispatch_register_id,
                    'details' => $details,
                    'prevDetails' => $prevDetails,
                    'seatingBoardingStr' => $seatingBoarding->keys()->sort()->implode(', '),
                    'seatingMixStr' => $currentOccupation->withOverlap ? $currentOccupation->seatingOccupied->keys()->sort()->implode(', ') : "",
                    'seatingReleaseStr' => $seatingReleased->keys()->sort()->implode(', '),
                    'seatingActivatedStr' => $seatingActivated->keys()->sort()->implode(', '),
                    'alarms' => (object)[
                        'withOverlap' => $currentOccupation->withOverlap,
                        'lockCamera' => $alertLockCam,
                        'counterLockCamera' => $counterLock,
                        'av' => $photo->data_properties
                    ],
                    'passengers' => (object)[
                        'byRoundTrips' => $personsByRoundTrips,
                        'totalInRoundTrip' => $personsByRoundTrip,
                        'total' => $totalPersons,
                        'totalSum' => $totalSum,
                        'totalSum2' => $totalSum2,
                    ]
                ]);

//                if ($details->occupation->count)
                $prevPhoto = $photo;
                $prevOccupation = $currentOccupation;
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
