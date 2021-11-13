<?php

namespace App\Services\Apps\Rocket\Photos;

use App;
use App\Events\App\Rocket\PhotoMapEvent;
use App\Models\Apps\Rocket\CurrentPhoto;
use App\Models\Apps\Rocket\Photo;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Models\Apps\Rocket\VehicleCamera;
use App\Models\Passengers\Passenger;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use App\PerfilSeat;
use App\Services\Apps\Rocket\Photos\Rekognition\Zone;
use App\Services\Apps\Rocket\SeatOccupationService;
use Carbon\Carbon;
use DB;
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
     * @var string
     */
    public $camera = 'all';

    /**
     * @var SeatOccupationService
     */
    private $seatOccupationService;

    /**
     * @var Collection
     */
    private $recognitionServices;

    /**
     * @var array
     */
    private $persistence = [];


    function __construct()
    {
        $this->storage = Storage::disk(self::DISK);
    }

    function setVehicle(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    function setCamera($camera)
    {
        if ($camera !== '' && $camera !== null) {
            $this->camera = $camera;
        }
        $this->setProfileSeating();
    }

    function setProfileSeating()
    {
        if ($this->camera !== '' && $this->camera !== null) {

            $profileSeating = $this->vehicle->getProfileSeating($this->camera);
            $profileSeating->setPersistence($this->persistence);

            $this->seatOccupationService = new SeatOccupationService($profileSeating);

            $this->recognitionServices = collect([]);
            foreach (['persons', 'faces'] as $type) {
                $this->recognitionServices->put($type, App::make("rocket.photo.rekognition.$type", ['profileSeating' => $profileSeating]));
            }
        }
    }

    function setPersistence($activate, $release)
    {
        if ($activate && $release) {
            $this->persistence = compact(['activate', 'release']);
        }
    }

    /**
     * @param Vehicle $vehicle
     * @param $camera
     * @param null $persistenceActivate
     * @param null $persistenceRelease
     * @return $this
     */
    function for(Vehicle $vehicle, $camera, $persistenceActivate = null, $persistenceRelease = null)
    {
        $this->setPersistence($persistenceActivate, $persistenceRelease);

        $this->setVehicle($vehicle);
        $this->setCamera($camera);

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
            $vId = $this->vehicle->id;
            $initialDate = $photo->date->toDateString();
            $finalDate = $photo->date->toDateTimeString();
            $photo->location_id = collect(DB::select("SELECT id from locations WHERE vehicle_id = $vId and date between '$initialDate' AND '$finalDate' ORDER BY date DESC LIMIT 1"))->first()->id;

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
            $success = true;
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
     * @param PhotoInterface $photo
     * @param Collection $historic
     * @return object|null
     */
    function getPhotoData(PhotoInterface $photo, $historic)
    {
        $historic = $historic->where('id', '<=', $photo->id);
        $lastPhoto = $historic->last();

        $details = $photo->getAPIFields('data-url');
        $details->occupation = $this->getOccupation($photo);

        $personsByRoundTrips = collect([]);
        $historicByTurns = $historic->groupBy('drId');

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

        return (object)[
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

    function getVehicleCameras(): array
    {
        $vehicleCameras = VehicleCamera::where('vehicle_id', $this->vehicle->id)->get();

        if ($vehicleCameras->count()) {
            return $vehicleCameras->pluck('camera')->toArray();
        }

        return [0];
    }

    function getPhotos($date = null, $camera = null)
    {
        $this->setCamera($camera);

        return Photo::whereVehicleAndDateAndSide($this->vehicle, $date ? $date : Carbon::now(), $this->camera)
//            ->whereBetween('id', [104073, 104173])
            //->where('id', 53717);
//            ->where('dispatch_register_id', '>', 1833559)
            ->limit(2000)
            ->get();
    }

    function processCount($date, $withHistoricPassengers = true)
    {
        $totalByCameras = 0;
        $allPhotos = $this->getPhotos($date);
        $drIds = $allPhotos->where('dispatch_register_id', '<>', null)->groupBy('dispatch_register_id')->keys();

        $historicByCameras = collect([]);

        foreach ($this->getVehicleCameras() as $sideCamera) {
            $this->setCamera($sideCamera); // Important for setProfileSeating() routine
            $data = $this->processPhotos($allPhotos->where('side', $sideCamera)->values())->where('drId', '<>', null);

            $historicByCameras->push($data->groupBy('drId'));
        }

        foreach ($drIds as $drId) {
            $countByRoundTrip = 0;
            foreach ($historicByCameras as $historicCamera) {
                $data = $historicCamera->get($drId);
                if ($data && $data->sortBy('date')->last()) {
                    $countByRoundTrip = $countByRoundTrip + $data->sortBy('time')->last()->passengers->totalInRoundTrip;
                }
            }

            DB::statement("UPDATE registrodespacho SET ignore_trigger = TRUE, registradora_llegada = $countByRoundTrip, final_sensor_counter = $countByRoundTrip WHERE id_registro = $drId");
        }

        if ($withHistoricPassengers) {
            $historic = $historicByCameras->collapse()->collapse();
            $totalByCamerasPrev = 0;
            DB::delete("DELETE FROM passengers WHERE date::DATE = '$date' AND vehicle_id = " . $this->vehicle->id);

            foreach ($drIds as $drId) {
                $historicDr = $historic->where('drId', $drId)->sortBy('time')->values();
                $historicDrByLocation = $historicDr->groupBy('details.location_id');

                foreach ($historicDrByLocation as $locationId => $locationPhotos) {
                    $totalCameras = $locationPhotos->groupBy('camera')->count();
                    $locationPhotos = collect($locationPhotos);
                    $totalByCameras = 0;
                    $totalInRoundTrip = 0;

                    $lastDatePhoto = null;

                    $occupation = (object)[
                        'percent' => 0,
                        'current' => collect([]),
                        'prev' => collect([]),
                        'activated' => collect([]),
                        'boarding' => collect([]),
                        'released' => collect([]),
                    ];

                    $totalSeating = 0;

                    foreach ($locationPhotos->groupBy('camera')->sort() as $camera => $photoData) {
                        $lastPhoto = $photoData->last();
                        $details = $lastPhoto->details;
                        $prevDetails = $lastPhoto->prevDetails;

                        $countPassengers = $lastPhoto->passengers;
                        $totalByCameras += $countPassengers->total;
                        $totalInRoundTrip += $countPassengers->totalInRoundTrip;

                        $lastDatePhoto = $details->date;

                        $occupation->percent += $details->occupation->percent / $totalCameras;
                        $occupation->current->push(explode(', ', $details->occupation->seatingOccupiedStr));
                        $occupation->activated->push(explode(', ', $details->occupation->seatingActivatedStr));
                        $occupation->boarding->push(explode(', ', $details->occupation->seatingBoardingStr));
                        $occupation->released->push(explode(', ', $details->occupation->seatingReleaseStr));

                        if ($prevDetails->occupation ?? false) {
                            $occupation->prev->push(explode(', ', $prevDetails->occupation->seatingOccupiedStr));
                        }

                        $profileSeating = $this->vehicle->getProfileSeating($camera);
                        $totalSeating += $profileSeating->occupation->count();
                    }

                    $occupation->percent = $totalSeating ? intval(100 * $totalInRoundTrip / $totalSeating) : 0;
                    $occupation->percent = $occupation->percent <= 100 ? $occupation->percent : 100;

                    $occupation->current = $occupation->current->flatten()->sort();
                    $occupation->activated = $occupation->activated->flatten()->sort();
                    $occupation->boarding = $occupation->boarding->flatten()->sort();
                    $occupation->released = $occupation->released->flatten()->sort();
                    $occupation->prev = $occupation->prev->flatten()->sort();

                    $ascents = $occupation->boarding->filter()->count();
                    $descents = $occupation->released->filter()->count();

                    $occupation->current = trim($occupation->current->implode(' '));
                    $occupation->activated = trim($occupation->activated->implode(' '));
                    $occupation->boarding = trim($occupation->boarding->implode(' '));
                    $occupation->released = trim($occupation->released->implode(' '));
                    $occupation->prev = trim($occupation->prev->implode(' '));

                    if ($drId == 1920798) { // Temporal fot vehicle 02 Montebello
                        $totalInRoundTrip = $totalInRoundTrip - 1;
                    }

                    $passenger = new Passenger([
                        'date' => $lastDatePhoto,
                        'dispatch_register_id' => $drId,
                        'location_id' => $locationId,
                        'vehicle_id' => $this->vehicle->id,
                        'total' => $totalByCameras,
                        'total_prev' => $totalByCamerasPrev,
                        'in_round_trip' => $totalInRoundTrip,
                        'ascents_in_round_trip' => $ascents,
                        'descents_in_round_trip' => $descents,
                        'tags' => collect([
                            'occupation' => $occupation
                        ])->toJson(),
                        'frame' => ''
                    ]);
                    $passenger->save();

                    $totalByCamerasPrev = $totalByCameras;
                }
            }
        }

        return collect([
            'total' => $totalByCameras,
            'totalPhotos' => $allPhotos->count()
        ]);
    }

    /**
     * @param $date
     * @return Collection
     */
    function getHistoric($date)
    {
        $photos = ($this->camera != null && $this->camera != 'all') ? $this->getPhotos($date) : collect([]);
        return $this->processPhotos($photos);
    }

    function processStatusDispatch(Photo $photo, Photo $prevPhoto)
    {
        $statusDispatch = 'none';
        if ($photo->id == $prevPhoto->id) {
            if ($photo->dispatch_register_id) {
                $statusDispatch = 'start';
            }
        } else {
            if ($photo->dispatch_register_id == $prevPhoto->dispatch_register_id) {
                if ($photo->dispatch_register_id) {
                    $statusDispatch = 'in';
                }
            } else {
                if ($photo->dispatch_register_id) {
                    $statusDispatch = 'start';
                } else if ($prevPhoto->dispatch_register_id) {
                    $statusDispatch = 'end';
                }
            }
        }

        return $statusDispatch;
    }

    /**
     * @param $seatingOccupied
     * @param $prevSeatingOccupied
     * @return mixed
     */
    function getBoardingSeating($seatingOccupied, $prevSeatingOccupied)
    {
        $seatingOccupiedNumbers = collect($seatingOccupied)->pluck('number');
        $prevSeatingOccupiedNumbers = collect($prevSeatingOccupied)->pluck('number');

        $boardingSeating = collect([]);
        foreach ($seatingOccupiedNumbers as $seat) {
            if (!$prevSeatingOccupiedNumbers->contains($seat)) {
                $boardingSeating->push($seat);
            }
        }

        return $boardingSeating->sort()->values();
    }

    /**
     * @param Collection|Photo[] $photos
     * @return Collection
     */
    function processPhotos(Collection $photos)
    {
        $initotal = Carbon::now();
        $photos = $photos->sortBy('date')->values();

        $historic = collect([]);

        $durations = collect([]);

        if ($photos->isNotEmpty()) {
            $prevPhoto = $photos->first();
            $prevOccupation = $this->getOccupation($prevPhoto);
            $prevDetails = $prevPhoto->getAPIFields('url');

            $personsByRoundTripT = 0;
            $totalPersonsT = 0;
            $totalSumOccupied = 0;
            $totalSumReleased = 0;

            $counterLock = 0;
            $alertLockCam = false;

            $pevRekognitionCounts = null;

            foreach ($photos as $index => $photo) {
                $ini = Carbon::now();

                if ($prevPhoto->dispatchRegister && !$prevPhoto->dispatchRegister->isActive()) {
                    $prevPhoto->dispatch_register_id = null;
                }

                if ($photo->dispatchRegister && !$photo->dispatchRegister->isActive()) {
                    $photo->dispatch_register_id = null;
                }

                $currentOccupation = $this->getOccupation($photo);
                if ($currentOccupation->withOverlap) {
//                    foreach ($prevOccupation->seatingOccupied as $seatNumber => $seatOccupied) {
//                        $currentOccupation->seatingOccupied->put($seatNumber, $seatOccupied);
//                    }
                }

                $durations->put('getOccupation', intval(Carbon::now()->diffInMicroseconds($ini) / 1000));
                $ini = Carbon::now();

                $statusDispatch = $this->processStatusDispatch($photo, $prevPhoto);
                $this->seatOccupationService->processPersistenceSeating($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, $currentOccupation->withOverlap, $statusDispatch);

                $seatingActivated = $this->seatOccupationService->getSeatingActivated($currentOccupation->seatingOccupied, $currentOccupation->withOverlap);
                $seatingReleased = $this->seatOccupationService->getSeatingReleased($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, $currentOccupation->withOverlap);

                $durations->put('getSeatingReleased', intval(Carbon::now()->diffInMicroseconds($ini) / 1000));
                $ini = Carbon::now();


                $details = $photo->getAPIFields('url');

                $durations->put('getAPIFields', intval(Carbon::now()->diffInMicroseconds($ini) / 1000));
                $ini = Carbon::now();

                $details->occupationOrig = clone $currentOccupation;
                $details->occupation = $currentOccupation;

                $totalSumOccupied += $seatingActivated->count();
                $totalSumReleased += $seatingReleased->count();

                $newPersons = $seatingActivated->count();

                $currentCount = $currentOccupation ? $currentOccupation->persons : 0;
                $prevCount = $prevOccupation && $photo->id != $prevPhoto->id ? $prevOccupation->persons : 0;

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
                    $personsByRoundTripT = $newPersons;
//                    $personsByRoundTripT = $currentOccupation->seatingOccupied->count();
                } else {
                    if ($photo->dispatch_register_id) {
                        $personsByRoundTripT += $newPersons;
                    }
                }

                if ($photo->dispatch_register_id) {
                    $roundTrip = $dr->round_trip;
                    $routeName = $dr->route->name;
                    $from = $dr->departure_time;
                    $to = $dr->arrival_time;
//                    $totalPersonsT += $firstPhotoInRoundTrip ? $currentOccupation->seatingOccupied->count() : $newPersons;
                    $totalPersonsT += $newPersons;
                }


                $this->processLockCam($photo, $counterLock, $alertLockCam);

                $durations->put('processLockCam', intval(Carbon::now()->diffInMicroseconds($ini) / 1000));
                $ini = Carbon::now();

                $details->occupation->seatingOccupiedStr = $details->occupation->seatingOccupied->keys()->sort()->implode(', ');
                $details->occupation->seatingBoardingStr = $this->getBoardingSeating($details->occupation->seatingOccupied, $prevDetails->occupation->seatingOccupied ?? [])->implode(', ');
                $details->occupation->seatingMixStr = $currentOccupation->withOverlap ? $currentOccupation->seatingOccupied->keys()->sort()->implode(', ') : "";
                $details->occupation->seatingReleaseStr = $seatingReleased->keys()->sort()->implode(', ');
                $details->occupation->seatingActivatedStr = $seatingActivated->keys()->sort()->implode(', ');


                $rekognitionCounts = $this->processRekognitionCounts($details, $prevDetails, $pevRekognitionCounts, $photo, $firstPhotoInRoundTrip);

                $durations->put('processRekognitionCounts', intval(Carbon::now()->diffInMicroseconds($ini) / 1000));
                $ini = Carbon::now();


                #### Default Count by topologies
                $personsByRoundTrip = $personsByRoundTripT;
                $totalPersons = $totalPersonsT;

                // Overwrite count by maximum criteria FACES or PERSONS
//                $personsByRoundTrip = $rekognitionCounts->values()->pluck('max')->max('value'); // get the max count between different rekognition types

                ###### FACES MAX CRITERIA
                $maximumCriteria = $rekognitionCounts->get('faces');
//                $personsByRoundTrip = $maximumCriteria->max->value;
//                $totalPersons = $maximumCriteria->total;

                ###### AVERAGES BETWEEN TOPOLOGIES AND FACES MAX CRITERIA
                $personsByRoundTrip = floor((($personsByRoundTripT ? : $maximumCriteria->max->value) + $maximumCriteria->max->value) / 2);
                $totalPersons = floor(($totalPersonsT + $maximumCriteria->total) / 2);

                $personsByRoundTrips = collect([])->push((object)[
                    'id' => $photo->dispatch_register_id,
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
//            $prevDraws = $prevDetails->occupation ? collect($prevDetails->occupation->draws) : collect([]);
//            $currentDraws = collect($details->occupation->draws);

            $prevDraws = $prevDetails->occupation ? collect($prevDetails->occupation->draws)->where('count', true) : collect([]);
            $currentDraws = collect($details->occupation->draws)->where('count', true);

            $prevCount = $prevDraws->where('type', $type)->count();
            $prevTotal = $pevRekognitionCounts ? collect($pevRekognitionCounts)->get($type)->total : 0;

            $count = $currentDraws->where('type', $type)->count();
            $diff = $count - $prevCount;
            $diff = $diff > 0 ? $diff : 0;

            $total = $prevTotal;

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
                    $total = $total + ($count - $maxDetection);
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
                        'routeName' => $photo->dispatchRegister ? $photo->dispatchRegister->route->name : '',
                        'roundTrip' => $photo->dispatchRegister ? $photo->dispatchRegister->round_trip : ''
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
     * @param bool $withMask
     * @param bool $withTitle
     * @return Image|mixed
     */
    function getFile(Photo $photo, $encode = "webp", $withEffect = false, $withMask = false, $withTitle = false)
    {
        $image = $photo->getImage($encode, $withEffect, $withMask, $withTitle, $withTitle);

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
            return Image::make(File::get('img/image-404.jpg'))->resize(300, 300)->response();
        } catch (FileNotFoundException $e) {
            return null;
        }
    }
}

