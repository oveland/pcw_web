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
use App\Models\Routes\DispatchRegister;
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

    /**
     * @var SeatOccupationService
     */
    private $seatOccupationService;


    /**
     * @var ProfileSeat
     */
    private $profileSeating;

    /**
     * @var Collection
     */
    private $recognitionServices;


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
        $this->seatOccupationService = new SeatOccupationService($vehicle);
        $this->profileSeating = $vehicle->profile_seating;

        $this->recognitionServices = collect([]);

        foreach (['persons', 'faces'] as $type) {
            $this->recognitionServices->put($type, App::make("rocket.photo.rekognition.$type", ['profileSeating' => $this->profileSeating]));
        }

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
            'uid' => 'required|unique:app_photos'
        ]);

        if ($validator->passes()) {
            $photo = new Photo($data->toArray());
            $photo->disk = self::DISK;
            $photo->date = Carbon::createFromFormat('Y-m-d H:i:s', $data->get('date'), 'America/Bogota');
            $photo->vehicle()->associate($this->vehicle);

            $currentLocation = $this->vehicle->currentLocation;
            $dr = $this->findDispatchRegisterByPhoto($photo);
            $photo->dispatch_register_id = $dr ? $dr->id : null;
            $photo->location_id = $currentLocation->location_id ?? null;

            $image = $this->decodeImageData($data->get('img'));

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

                $success = $currentPhoto->save();
                if ($success) $message = "Photo saved successfully";

                if (!App::environment('local')) {
//                    $this->notifyToMap();
                }
            }
        } else {
            $message = collect($validator->errors())->flatten()->implode(' ');
        }

        return (object)[
            'response' => (object)[
                'success' => $success,
                'message' => $message,
            ],
//            'photo' => $success ? $photo->getAPIFields() : null
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
            $historicByTurns = $historic->groupBy('drId');
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
                        'from' => $dispatchRegister->departure_time,
                        'to' => $dispatchRegister->arrival_time,
                        'count' => $lastHistoricByTurn->passengers->totalInRoundTrip,
                        'firstHistoricByTurn' => $firstHistoricByTurn,
                        'historic' => collect($historicByTurn)->only(['time', 'passengers']),
                        'lastHistoricByTurn' => $lastHistoricByTurn,

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
                'alarms' => $lastPhoto->alarms ?? null,
                'rekognitionCounts' => $lastPhoto->rekognitionCounts ?? null,
                'passengers' => (object)[
                    'byRoundTrips' => $personsByRoundTrips,
                    'total' => $personsByRoundTrips ? $personsByRoundTrips->sum('count') : 0,
                    'totalSumOccupied' => $historic->count() ? $historic->last()->passengers->totalSumOccupied : 0,
                    'totalSumReleased' => $historic->count() ? $historic->last()->passengers->totalSumReleased : 0
                ]
            ];
        }

        return $photoData;
    }


    private function getOccupation(PhotoInterface $photo, $type = self::REKOGNITION_TYPE)
    {

        switch ($type) {
            case 'persons':
            case 'faces':
                return $this->recognitionServices->get($type)->processOccupation($photo);
                break;
            case 'persons_and_faces':
                $personsRekognition = $this->recognitionServices->get('persons');
                $facesRekognition = $this->recognitionServices->get('faces');

                $personsOccupation = $personsRekognition->processOccupation($photo);
                $facesOccupation = $facesRekognition->processOccupation($photo);

                $occupation = $personsOccupation;

                if ($facesOccupation) {
                    $occupation->draws = $occupation->draws->merge($facesOccupation->draws);
                }

                $occupation = $this->filterDrawsInsideOverlap($occupation);
//                $occupation->type = $type;

                return $personsRekognition->occupationParams($occupation);

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
     * @param Photo $photo
     * @return DispatchRegister
     */
    public function findDispatchRegisterByPhoto(Photo $photo)
    {
        $dr = DispatchRegister::where('date', $photo->date->toDateString())
            ->where('vehicle_id', $photo->vehicle->id)
            ->where('departure_time', '<=', $photo->date->toTimeString())
//                ->where('arrival_time', '>=', $photo->date->toTimeString())
            ->orderByDesc('departure_time')
            ->active()->first();

        if ($dr && $dr->complete() && $dr->arrival_time < $photo->date->toTimeString()) {
            return null;
        }

        return $dr;
    }

    /**
     * @param null $date
     * @param int $camera
     * @return Collection
     */
    function getHistoric($date = null, $camera = null)
    {
        $photos = Photo::whereVehicleAndDateAndSide($this->vehicle, $date ? $date : Carbon::now(), $camera)
//            ->whereBetween('id', [77495, 77503])
            //->where('id', 53717);
            ->get();

        if($camera == null || $camera == 'all') {
            $historicDrCamera0 = $this->processPhotos($photos->where('side', '0'))->where('drId', '<>', null)->groupBy('drId');
            $historicDrCamera1 = $this->processPhotos($photos->where('side', '1'))->where('drId', '<>', null)->groupBy('drId');

            foreach ($historicDrCamera0 as $drId => $h0){
                $maxCamera0 = 0;
                if ($h0->sortBy('date')->last()) {
                    $maxCamera0 = $h0->sortBy('time')->last()->passengers->totalInRoundTrip;
                }

                $h1 = $historicDrCamera1->get($drId);

                $maxCamera1 = 0;
                if ($h1 && $h1->sortBy('date')->last()) {
                    $maxCamera1 = $h1->sortBy('time')->last()->passengers->totalInRoundTrip;
                }

                $totalDr = $maxCamera0 + $maxCamera1;

                \DB::statement("UPDATE registrodespacho SET ignore_trigger = TRUE, registradora_llegada = $totalDr, final_sensor_counter = $totalDr WHERE id_registro = $drId");
            }
        }

        return $this->processPhotos($photos);
    }

    /**
     * @param Collection|Photo[] $photos
     * @return Collection
     */
    function processPhotos(Collection $photos) {
        $historic = collect([]);

        if ($photos->isNotEmpty()) {
            $prevPhoto = $photos->first();
            $prevOccupation = $this->getOccupation($prevPhoto);
            $prevDetails = $prevPhoto->getAPIFields('url');

            $personsByRoundTrip = 0;
            $totalPersons = 0;
            $totalSumOccupied = 0;
            $totalSumReleased = 0;

            $counterLock = 0;
            $alertLockCam = false;

            $pevRekognitionCounts = null;

            foreach ($photos->sortBy('date') as $photo) {

                if($photo->dispatchRegister && !$photo->dispatchRegister->isActive()) {
                    $photo->dispatch_register_id = null;
                }


                $currentOccupation = $this->getOccupation($photo);

                $details = $photo->getAPIFields('url');

                $seatingActivated = collect([]);
                $seatingReleased = collect([]);

                if ($currentOccupation->withOverlap) {
//                    foreach ($prevOccupation->seatingOccupied as $seatNumber => $seatOccupied) {
//                        $currentOccupation->seatingOccupied->put($seatNumber, $seatOccupied);
//                    }
                }

                $this->seatOccupationService->processPersistenceSeating($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, $currentOccupation->withOverlap);

                $seatingActivated = $this->seatOccupationService->getSeatingActivated($currentOccupation->seatingOccupied, $currentOccupation->withOverlap);
                $seatingReleased = $this->seatOccupationService->getSeatingReleased($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, $currentOccupation->withOverlap);


                $details->occupationOrig = clone $currentOccupation;
                $details->occupation = $currentOccupation;

                $totalSumOccupied += $seatingActivated->count();
                $totalSumReleased += $seatingReleased->count();

                $newPersons = $seatingActivated->count();

                $currentCount = $currentOccupation ? $currentOccupation->persons : 0;
                $prevCount = $prevOccupation ? $prevOccupation->persons : 0;

                if ($photo->persons === null) {
                    $photo->persons = $currentCount;
                    $photo->save();
                }

                $roundTrip = null;
                $routeName = null;
                $from = null;
                $to = null;

                $dr = $photo->dispatchRegister;

//                if ($dr === null) {
//                    $dr = $this->findDispatchRegisterByPhoto($photo);
//                    if ($dr) {
//                        $photo->dispatchRegister()->associate($dr);
//                        $photo->save();
//                        $photo->refresh();
//                    }
//                }

                $firstPhotoInRoundTrip = $photo->dispatch_register_id != $prevPhoto->dispatch_register_id || $prevPhoto->id == $photo->id;


                if ($firstPhotoInRoundTrip) {
                    $personsByRoundTrip = $newPersons;
//                    $personsByRoundTrip = $currentOccupation->seatingOccupied->count();
                } else {
                    if ($dr) {
                        $personsByRoundTrip += $newPersons;
                    }
                }

                if ($dr) {
                    $roundTrip = $dr->round_trip;
                    $routeName = $dr->route->name;
                    $from = $dr->departure_time;
                    $to = $dr->arrival_time;
//                    $totalPersons += $firstPhotoInRoundTrip ? $currentOccupation->seatingOccupied->count() : $newPersons;
                    $totalPersons += $newPersons;
                }


                $this->processLockCam($photo, $counterLock, $alertLockCam);

                $details->occupation->seatingOccupiedStr = $details->occupation->seatingOccupied->keys()->sort()->implode(', ');
                $details->occupation->seatingBoardingStr = $details->occupation->seatingOccupied->keys()->sort()->implode(', ');
                $details->occupation->seatingMixStr = $currentOccupation->withOverlap ? $currentOccupation->seatingOccupied->keys()->sort()->implode(', ') : "";
                $details->occupation->seatingReleaseStr = $seatingReleased->keys()->sort()->implode(', ');
                $details->occupation->seatingActivatedStr = $seatingActivated->keys()->sort()->implode(', ');


                $rekognitionCounts = $this->processRekognitionCounts($details, $prevDetails, $pevRekognitionCounts, $photo, $firstPhotoInRoundTrip);

                $personsByRoundTrip = $rekognitionCounts->values()->pluck('max')->max('value');

                $personsByRoundTrips = collect([])->push((object)[
                    'id' => $dr ? $dr->id : null,
                    'number' => $roundTrip,
                    'route' => $routeName,
                    'from' => $from,
                    'to' => $to,
                    'persons' => $personsByRoundTrip,
                    'count' => $personsByRoundTrip,

                    'prevCount' => $prevCount,
                    'currentCount' => $currentCount,
                    'newPersons' => $newPersons,
                    'prevId' => $prevPhoto->id,
                ]);

                $historic->push((object)[
                    'id' => $photo->id,
                    'camera' => $photo->side,
                    'time' => $photo->date->format('H:i:s.u') . '' . $photo->id,
                    'drId' => $photo->dispatch_register_id,
                    'details' => $details,
                    'rekognitionCounts' => $rekognitionCounts,
                    'prevDetails' => $prevDetails,
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
                        'totalSumOccupied' => $totalSumOccupied,
                        'totalSumReleased' => $totalSumReleased,
                    ]
                ]);

                $prevPhoto = $photo;
                $prevOccupation = $currentOccupation;
                $prevDetails = $details;

                $pevRekognitionCounts = $rekognitionCounts;
            }
        }

        return $historic;
    }

    public function processRekognitionCounts($details, $prevDetails, $pevRekognitionCounts = null, Photo $photo, $firstPhotoInRoundTrip)
    {
        $rekognitionCounts = collect([]);

        $types = [
            'persons' => (object)[
                'name' => __('persons'),
                'icon' => 'fa fa-male',
                'persistence' => (object)[
                    'count' => 1,
                    'empty' => 1,
                ]
            ],
            'faces' => (object)[
                'name' => __('faces'),
                'icon' => 'icon-emoticon-smile',
                'persistence' => (object)[
                    'count' => 1,
                    'empty' => 1
                ]
            ]
        ];

        foreach ($types as $type => $description) {
            $prevDraws = $prevDetails->occupation ? collect($prevDetails->occupation->draws) : collect([]);
            $currentDraws = collect($details->occupation->draws);

            //$prevDraws = $prevDetails->occupation ? collect($prevDetails->occupation->draws)->where('count', true) : collect([]);
            //$currentDraws = collect($details->occupation->draws)->where('count', true);

            $prevCount = $prevDraws->where('type', $type)->count();
            $prevTotal = $pevRekognitionCounts ? collect($pevRekognitionCounts)->get($type)->total : 0;

            $count = $currentDraws->where('type', $type)->count();
            $diff = $count - $prevCount;
            $diff = $diff > 0 ? $diff : 0;
            $total = $prevTotal + $diff;

            /* With Persistence */

            // Prev persistence
            $ppCounter = $pevRekognitionCounts ? collect($pevRekognitionCounts)->get($type)->persistence->counter : 0;
            $ppTotal = $pevRekognitionCounts ? collect($pevRekognitionCounts)->get($type)->persistence->total : 0;
            $ppCount = $pevRekognitionCounts ? collect($pevRekognitionCounts)->get($type)->persistence->count : 0;

            // Current Persistence
            $pCounter = $count == $prevCount ? $ppCounter + 1 : 0;

            $pCount = $pCounter == $description->persistence->count || ($ppCount < $count) ? $count : $ppCount; // Negative Persistence
//            $pCount = $pCounter == $description->persistence->count || ($ppCount > $count) ? $count : $ppCount; // Positive Persistence
//            $pCount = $pCounter == $description->persistence->count ? $count : $ppCount; // Both Persistence

            $pDifference = $pCount - $ppCount;
            $pDifference = $pDifference > 0 ? $pDifference : 0;
            $pTotal = $ppTotal + $pDifference;


            // Calculate max in round trip
            $prevMaxDetection = $pevRekognitionCounts ? collect($pevRekognitionCounts)->get($type)->max->detection : 0;
            $prevMaxPhotoId = $pevRekognitionCounts ? collect($pevRekognitionCounts)->get($type)->max->photoId : '';

            $maxValue = 0;

            $maxPhotoId = $prevMaxPhotoId;
            $maxDetection = $prevMaxDetection;

            if ($firstPhotoInRoundTrip) {
                $maxDetection = 0;
            }

            if ($photo->dispatch_register_id) {
                if ($count > $prevMaxDetection) {
                    $maxDetection = $count;
                    $maxPhotoId = $photo->id;
                }

                $maxValue = $maxDetection > 0 ? $maxDetection : 0;
            }

            $rekognitionCounts->put($type, (object)[
                'description' => $description,
                'count' => $count,
                'total' => $total,
                'persistence' => (object)[
                    'count' => $pCount,
                    'counter' => $pCounter,
                    'diff' => $pDifference,
                    'total' => $pTotal
                ],
                'max' => (object)[
                    'photoId' => $maxPhotoId,
                    'value' => $maxValue,
                    'detection' => $maxDetection,
                    'dr' => (object)[
                        'id' => $photo->dispatch_register_id,
                        'routeName' => $photo->dispatchRegister? $photo->dispatchRegister->route->name : '',
                        'roundTrip' => $photo->dispatchRegister? $photo->dispatchRegister->round_trip : ''
                    ]
                ]
            ]);
        }

        return $rekognitionCounts;
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
        $image = $photo->getImage($encode, $withEffect, request()->get('with-mask'));

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
}
