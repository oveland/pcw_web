<?php

namespace App\Services\Apps\Rocket\Photos;

use App;
use App\Events\App\Rocket\PhotoMapEvent;
use App\Http\Controllers\Utils\Geolocation;
use App\Models\Apps\Rocket\CurrentPhoto;
use App\Models\Apps\Rocket\Photo;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Models\Apps\Rocket\VehicleCamera;
use App\Models\Passengers\Passenger;
use App\Models\Routes\ControlPoint;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\CurrentLocation;
use App\Models\Vehicles\Vehicle;
use App\Models\Company\Company;
use App\PerfilSeat;
use App\Services\Apps\Rocket\Photos\Rekognition\Zone;
use App\Services\Apps\Rocket\SeatOccupationService;
use Carbon\Carbon;
use DB;
use Exception;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Image;
use Storage;
use Validator;
use App\Models\Vehicles\Location;

class PhotoService
{
    const DISK = 's3';
    const REKOGNITION_TYPE = 'persons_and_faces';
//    const REKOGNITION_TYPE = 'persons';
//    const REKOGNITION_TYPE = 'faces';

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
     * @var string
     */
    public $date = null;

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

    function setDate($date)
    {
        $this->date = $date ?: Carbon::now()->toDateString();
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

            $profileSeating = $this->vehicle->getProfileSeating($this->camera, $this->date);
            $profileSeating->setPersistence($this->persistence);

            $configProfile = $this->vehicle->getConfigProfile($this->camera, $this->date, $profileSeating);

            $this->seatOccupationService = new SeatOccupationService($configProfile);

            $this->recognitionServices = collect([]);
            foreach (['persons', 'faces'] as $type) {
                $this->recognitionServices->put($type, App::make("rocket.photo.rekognition.$type", [
                    'profileSeating' => $profileSeating,
                    'configProfile' => $configProfile,
                ]));
            }
        }
    }

    function setPersistence($activate, $release)
    {
        if ($activate && $release) {
            $this->persistence = compact(['activate', 'release']);
        }
    }

    function for(Vehicle $vehicle, $camera, $persistenceActivate = null, $persistenceRelease = null, $date = null)
    {
        $this->setPersistence($persistenceActivate, $persistenceRelease);

        $this->setVehicle($vehicle);
        $this->setDate($date);
        $this->setCamera($camera);

        return $this;
    }

    /**
     * @param $data
     * @param false $withPhoto
     * @return object
     * @throws Exception
     */
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

                    $success = $photo->processRekognition(false, null, true);

                    if ($success) $message = "Photo $uid saved successfully";
                    else $message = "Photo $uid saved successfully with error in processRekognition";
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
                $success = $photoSaved->processRekognition(false, null, true);
                if ($success === true) $message = "Photo $uid updated successfully";
                else $message = "Photo $uid is currently saved but processRekognition has error";
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

    function notifyToMap()
    {
        $currentPhoto = CurrentPhoto::findByVehicle($this->vehicle);
        $historic = $this->getHistoric();

        event(new PhotoMapEvent($this->vehicle,
            [
                'type' => 'historic',
                'vehicle' => $this->vehicle->getAPIFields(),
                'historic' => $historic->sortByDesc('ts')->values()->toArray(),
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

        $details = $photo->getAPIFields('url', true, true);
        $details->occupation = $this->getOccupation($photo);

        $personsByRoundTrips = collect([]);
        $historicByTurns = $historic->groupBy('drId');

        foreach ($historicByTurns as $dr => $historicByTurn) {
            $firstHistoricByTurn = $historicByTurn->sortBy('ts')->first();
            $lastHistoricByTurn = $historicByTurn->sortBy('ts')->last();
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
                    'historic' => collect($historicByTurn)->only(['ts', 'time', 'passengers']),
                    'lastHistoricByTurn' => $lastHistoricByTurn,

                    'prevCount' => $firstHistoricByTurn->passengers->totalInRoundTrip,
                    'currentCount' => $lastHistoricByTurn->passengers->totalInRoundTrip
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

    function processLockCam(PhotoInterface $currentPhoto, &$counterLock, &$alert)
    {
        $currentAvBrightness = $currentPhoto->data_properties->avBrightness ?? null;

        if ($currentAvBrightness !== null && $currentAvBrightness < 2) {
            $counterLock++;
        } else {
            $counterLock = 0;
        }
        $alert = $counterLock >= 3;
    }

    function findDispatchRegisterByPhoto(Photo $photo, CurrentLocation $currentLocation = null)
    {
        $dr = DispatchRegister::where('date', $photo->date->toDateString())
            ->where('vehicle_id', $photo->vehicle->id)
            ->where('route_id', '<>', null)
            ->where('departure_time', '<=', $photo->date->toTimeString())
//                ->where('arrival_time', '>=', $photo->date->toTimeString())
            ->orderByDesc('departure_time')
            ->active()->first();

        if (!$dr && $currentLocation) $dr = DispatchRegister::find($currentLocation->dispatch_register_id);

        if ($dr && $dr->complete() && $dr->arrival_time < $photo->date->toTimeString()) {
            return null;
        }

        return $dr;
    }

    function getDispatchRegisters()
    {
        if (!$this->date || !$this->vehicle) return collect([]);

        return DispatchRegister::where('date', $this->date)
            ->where('vehicle_id', $this->vehicle->id)
            ->where('route_id', '<>', null)
            ->active()
            ->orderBy('departure_time')
            ->get();
    }

    function belongsToLargeRoute($drs) {
        return collect($drs)->filter(function(DispatchRegister $dr) {
            return $dr->route->isLarge();
        })->count() > 0;
    }

    function getVehicleCameras(): array
    {
        $vehicleCameras = VehicleCamera::where('vehicle_id', $this->vehicle->id)->get();

        if ($vehicleCameras->count()) {
            return $vehicleCameras->pluck('camera')->toArray();
        }

        return [0];
    }

    function getPhotos($camera = null)
    {
        $vehicle = $this->vehicle;
        $this->setCamera($camera);

        $activeDrs = $this->getDispatchRegisters();

        $limit = 4000;

        if(request()->get('limit')) {
            $limit = intval(request()->get('limit'));
        }

        $photosQuery = Photo::whereVehicleAndDateAndSide($vehicle, $this->date ?: Carbon::now(), $this->camera)
            ->orWhere(function ($query) use ($activeDrs) {
                $side = $this->camera;
                if ($side !== null && $side != 'all' && $side !== "") {
                    $query = $query->whereSide($side);
                }
                return $query->whereIn('dispatch_register_id', $activeDrs->pluck('id'));
            })
            ->with('dispatchRegister')
            ->limit($limit)
            ->orderBy('date');



        if ($this->belongsToLargeRoute($activeDrs)) {
            $photosQuery = $photosQuery
//                ->limit(10)
                ->with(['location' => function ($query) {
                    return $query->select(['id', 'date', 'distance', 'latitude', 'longitude'])
                        ->with(['report' => function ($query) {
                            return $query->select('id', 'distancem', 'location_id', 'control_point_id')
                                ->with(['controlPoint' => function ($query) {
                                    return $query->select('id', 'name', 'latitude', 'longitude', 'order');
                                }]);
                        }]);
                }]);
        }

        $photos = $photosQuery
            ->get()
            ->map(function (Photo $photo) use ($vehicle) {
                $dr = $photo->dispatchRegister;
                //$dr = ($dr && $dr->route_id) ? $dr : null;
                $dr = ($dr && $dr->route_id && $dr->date == $this->date) ? $dr : null;

                $photo->dispatchRegister()->associate($dr);

                if ($dr && $dr->route->isLarge()) {
                    /**
                     * @var ControlPoint $cp
                     */
                    $cp = $photo->location && $photo->location->report && $photo->location->report->controlPoint ? $photo->location->report->controlPoint : null;
                    $lc = $photo->location;

                    if ($cp && $cp->order > 0 && $dr && $dr->isActive()) {
                        $r = $lc->report;
                        $distance = Geolocation::getDistance($lc->latitude, $lc->longitude, $cp->latitude, $cp->longitude);
                        if ($distance < $dr->route->distance_threshold) {
//                            $routeName = $dr->route->name;
//                            dump("$lc->date • $routeName • $r->distancem m. vs distance to CP $cp->name = $distance");
                            $photo->cp = $cp;
                        }
                    }
                }

                return $photo;
            });

        return $photos->sortBy('date');
    }

    function processMultiTariff(Collection $allHistoric)
    {
        $historicByDr = $allHistoric->groupBy('drId');
        foreach ($historicByDr as $drId => $historic) {
            DB::statement("DELETE FROM history_seats WHERE dispatch_register_id = $drId AND vehicle_id = " . $this->vehicle->id);
        }

        $countTotal = 0;
        $countByTurn = 0;

        foreach ($historicByDr as $drId => $historic) {
            // echo "<br><br>";
            $countByTurn = 0;
            $registers = collect([]);

            $dr = DispatchRegister::find($drId);

            foreach ($historic->sortBy('ts') as $photo) {
                $vehicle = $this->vehicle;
                $details = $photo->details;

                $date = Carbon::make($details->date);
                $time = $date->toTimeString();

                $location = Location::select(['date', 'distance', 'latitude', 'longitude'])->where('id', $details->location_id)->first();
                $occupation = $details->occupation;

                $latitude = $location ? $location->latitude : 'null';
                $longitude = $location ? $location->longitude : 'null';
                $distance = $location ? $location->distance : 0;

                $seatingActivated = explode(', ', $occupation->seatingActivatedStr);
                $seatingReleased = explode(', ', $occupation->seatingReleaseStr);

//                $lc = $location;
//                dump("$photo->id • $lc->date • $lc->distance • $occupation->seatingActivatedStr vs $occupation->seatingReleaseStr");

                if ($occupation->seatingActivatedStr) {
                    foreach ($seatingActivated as $seat) {
                        $countByTurn++;
                        $countTotal++;

                        // echo "Active seat = $seat at $date | $info | Count = $countByTurn <br>";
                        $seat = intval($seat);

                        $insert = DB::select("
                            INSERT INTO history_seats (plate, seat, date, time, active_time, active_km, vehicle_id, dispatch_register_id, active_latitude, active_longitude, start_photo_id) 
                            VALUES ('$vehicle->plate', $seat, '" . $date->toDateString() . "', '$time', '$date', $distance, $vehicle->id, $drId, $latitude, $longitude, $photo->id) RETURNING id
                        ");
                        $id = collect($insert)->first()->id;

                        $registers->put($seat, (object)[
                            'id' => $id,
                            'seat' => $seat,
                            'camera' => $photo->camera,
                            'arrivedTime' => $dr->date_end . " " . $dr->arrival_time,
                            'routeDistance' => $dr->route->distance_in_meters,
                        ]);
                    }
                }

                if ($occupation->seatingReleaseStr) {
                    foreach ($seatingReleased as $seat) {
                        if ($registers->get($seat)) {
                            $register = $registers->get($seat);
                            // echo ".............. Release seat = $seat at $date | $info | $countByTurn <br>";
                            if ($register) {
                                $id = $register->id;
                                if ($id) {
                                    DB::statement("UPDATE history_seats SET inactive_time = '$date', inactive_km = $distance, inactive_latitude = $latitude, inactive_longitude = $longitude, end_photo_id = $photo->id WHERE id = $id");
                                }
                            }

                            $registers->forget($seat);
                        }
                    }
                }
            }

            $lastLocation = Location::select(['date', 'distance', 'latitude', 'longitude'])->where('dispatch_register_id', $drId)->orderBy('date', 'desc')->first();

            $latitude = $lastLocation ? $lastLocation->latitude : 'null';
            $longitude = $lastLocation ? $lastLocation->longitude : 'null';

            foreach ($registers as $register) {
                $id = $register->id;
                $arrivedTime = $register->arrivedTime;
                $routeDistance = $register->routeDistance;

                $lastPhoto = Photo::select(['id'])
                    ->where('dispatch_register_id', $drId)
                    ->where('side', $register->camera)
                    ->orderBy('date', 'desc')->first();

                DB::statement("UPDATE history_seats SET inactive_time = '$arrivedTime', inactive_km = $routeDistance, inactive_latitude = $latitude, inactive_longitude = $longitude, end_photo_id = $lastPhoto->id WHERE id = $id");
            }
        }
    }

    function processCount($withHistoricPassengers = true, $withMultiTariff = false)
    {
        $totalByCameras = 0;
        $allPhotos = $this->getPhotos();

//        echo "Process " . $allPhotos->count() . " photos. Date = $this->date , Camera = $this->camera \n";

        $drIds = $allPhotos->where('dispatch_register_id', '<>', null)->groupBy('dispatch_register_id')->keys();

        $historic = collect([]);
        $historicByCameras = collect([]);
        foreach ($this->getVehicleCameras() as $sideCamera) {
            $this->setCamera($sideCamera); // Important for setProfileSeating() routine
            $data = $this->processPhotos($allPhotos->where('side', $sideCamera)->values())->where('drId', '<>', null);

            $historic->push($data);
            $historicByCameras->push($data->groupBy('drId'));
        }
        $historic = $historic->collapse();

        if ($withMultiTariff || true) {
            $this->processMultiTariff($historic);
        }

        foreach ($drIds as $drId) {
            $dr = DispatchRegister::find($drId);
            $route = $dr->route;

            $countByRoundTrip = 0;
            $countMaxByRoundTrip = 0;
            foreach ($historicByCameras as $historicCamera) {
                $data = $historicCamera->get($drId);

                if ($data) {
                    $lastHistoricData = $data->sortBy('ts')->last();

                    $countByRoundTrip = $countByRoundTrip + $lastHistoricData->passengers->totalInRoundTrip;
                    $countMaxByRoundTrip = $countMaxByRoundTrip + $lastHistoricData->passengers->maxPersonByRoundTrip;
                }
            }

//            if ($dr->isActive()) echo " • DR $dr->id ($dr->status) $dr->date dep: $dr->departure_time - $dr->arrival_time $route->name Count: $countByRoundTrip \n";

            $drObs = DispatchRegister::find($drId)->getObservation('rocket_passengers');
            $drObs->value = $countByRoundTrip;
            $drObs->observation = '';
            $drObs->user_id = 2018101392; // Set user BOOTPCW
            $drObs->save();

            DB::statement("UPDATE registrodespacho SET ignore_trigger = TRUE, final_sensor_counter = $countByRoundTrip WHERE id_registro = $drId");
            DB::statement("UPDATE registrodespacho SET ignore_trigger = TRUE, registradora_llegada = $countByRoundTrip WHERE id_registro = $drId AND id_empresa <> 39");
            if ($this->vehicle->company_id == 39) {
                DB::statement("UPDATE registrodespacho SET ignore_trigger = TRUE, final_front_sensor_counter = $countMaxByRoundTrip WHERE id_registro = $drId");
            }
        }

        if ($withHistoricPassengers) {
            foreach ($drIds as $drId) {
                DB::delete("DELETE FROM passengers WHERE dispatch_register_id = $drId");
            }

            //$historic = $historicByCameras->collapse()->collapse();
            $totalByCamerasPrev = 0;
            foreach ($drIds as $drId) {
                $historicDr = $historic->where('drId', $drId)->sortBy('ts')->values();
                $historicDrByLocation = $historicDr->groupBy('details.location_id');

                $totalByRoundTripPrev = 0;

                foreach ($historicDrByLocation as $locationId => $locationPhotos) {
                    $totalCameras = $locationPhotos->groupBy('camera')->count();
                    $locationPhotos = collect($locationPhotos)->sortBy('ts');
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
                        $lastPhoto = $photoData->sortBy('ts')->last();
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

                        $profileSeating = $this->vehicle->getProfileSeating($camera, $this->date);
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


                    $totalByCameras = max([$totalByCameras, $totalByCamerasPrev]);
                    $totalInRoundTrip = max([$totalInRoundTrip, $totalByRoundTripPrev]);

                    $passenger = new Passenger([
                        'date' => $lastDatePhoto,
                        'dispatch_register_id' => $drId,
                        'location_id' => intval($locationId) ?: null,
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
                    $totalByRoundTripPrev = $totalInRoundTrip;
                }
            }
        }

        return collect([
            'total' => $totalByCameras,
            'totalPhotos' => $allPhotos->count()
        ]);
    }

    function getHistoric($forReportPhotos = false)
    {
        $photos = ($this->camera != null && $this->camera != 'all' || $forReportPhotos) ? $this->getPhotos() : collect([]);
        return $this->processPhotos($photos);
    }

    /**
     * @param Photo $photo
     * @param Photo $prevPhoto
     * @return StatusDR
     */
    function processStatusDispatch(Photo $photo, Photo $prevPhoto)
    {
        if ($prevPhoto->dispatchRegister && !$prevPhoto->dispatchRegister->isActive()) {
            $prevPhoto->dispatch_register_id = null;
        }

        if ($photo->dispatchRegister && !$photo->dispatchRegister->isActive()) {
            $photo->dispatch_register_id = null;
        }

        $statusDR = new StatusDR();

        $statusText = 'none';
        if ($photo->id == $prevPhoto->id) {
            if ($photo->dispatch_register_id) {
                $statusText = 'start';
                $statusDR->start = true;
            }
        } else {
            if ($photo->dispatch_register_id == $prevPhoto->dispatch_register_id) {
                if ($photo->dispatch_register_id) {
                    $statusText = 'in';
                    $statusDR->in = true;
                }
            } else {
                if ($photo->dispatch_register_id) {
                    $statusText = 'start';
                    $statusDR->start = true;
                } else if ($prevPhoto->dispatch_register_id) {
                    $statusText = 'end';
                    $statusDR->end = true;
                }
            }
        }

        $dr = $photo->dispatchRegister;

        if ($dr && $dr->route->isLarge()) {
            $cp = $photo->cp ?? null;
            $prevCp = $prevPhoto->cp ?? null;

            $statusDR->cp = $cp;

            if ($prevCp && !$cp) {
                $statusText = 'start';
                $statusDR->start = true;
                $statusDR->end = false;
            } else if ($cp) {
                $statusText = 'end';
                $statusDR->start = false;
                $statusDR->end = true;
//                $dr = null;
            }
            $lc = $photo->location;
//            dump("$photo->id • $photo->date • $lc->date • $lc->distance • $photo->side • $statusText • $cp vs $prevCp");
        }

        $statusDR->text = $statusText;
        $statusDR->dr = $dr;


//        $statysDR->cp = $photo->cp;

//        if ($statusDR->dr === null) {
//            $statusDR->dr = $this->findDispatchRegisterByPhoto($photo);
//            if ($statusDR->dr) {
//                $photo->dispatchRegister()->associate($statusDR->dr);
//                $photo->save();
//                $photo->refresh();
//            }
//        }

        return $statusDR;
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
        $photos = $photos->sortBy('date')->values();

        $historic = collect([]);
        $activationCounts = collect([]);

        if ($photos->isNotEmpty()) {
            $prevPhoto = $photos->first();
            $prevOccupation = $this->getOccupation($prevPhoto);
            $prevDetails = $prevPhoto->getAPIFields('url');

            $personsByRoundTripT1 = 0;
            $personsByRoundTripT2 = 0;
            $personsByRoundTripActivationCounts = 0;
            $totalPersonsActivationCounts = 0;

            $totalPersonsT1 = 0;
            $totalPersonsT2 = 0;
            $totalSumOccupied = 0;
            $totalSumReleased = 0;
            $maxPersonByRoundTrip = 0;

            $totalPersonsInPrevDrs = 0;

            $counterLock = 0;
            $alertLockCam = false;

            $pevRekognitionCounts = null;

            foreach ($photos as $index => $photo) {
                $statusDR = $this->processStatusDispatch($photo, $prevPhoto);

                $currentOccupation = $this->getOccupation($photo);
//                if ($currentOccupation->withOverlap) {
//                    foreach ($prevOccupation->seatingOccupied as $seatNumber => $seatOccupied) {
//                        $currentOccupation->seatingOccupied->put($seatNumber, $seatOccupied);
//                    }
//                }


                $this->seatOccupationService->processPersistenceSeating($currentOccupation, $prevOccupation, $statusDR);

                $seatingActivated = $this->seatOccupationService->getSeatingActivated($currentOccupation->seatingOccupied, $currentOccupation->withOverlap);
                $totalSumOccupied += $seatingActivated->count();

                $seatingReleased = $this->seatOccupationService->getSeatingReleased($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, $currentOccupation->withOverlap);
                $totalSumReleased += $seatingReleased->count();

                $seatingCounted = $this->seatOccupationService->getSeatingCounted($currentOccupation->seatingCounts, $currentOccupation->withOverlap);
                $prevSeatingCounted = $this->seatOccupationService->getSeatingCounted($prevOccupation->seatingCounts, $currentOccupation->withOverlap);

                if ($statusDR->isActive()) {
                    $newPersonsT1 = $seatingActivated->count(); // By Criteria of Topologies by persistence
                    $newPersonsT2 = $statusDR->start ? $seatingCounted->count() : $seatingCounted->count() - $prevSeatingCounted->count(); // By Criteria of Topologies 2 (Nivel de llenado de Vasos)






//                    $newPersons = max($newPersons, $newPersons2);

                    if ($statusDR->start) {
                        $personsByRoundTripT1 = $newPersonsT1;
                        $personsByRoundTripT2 = $newPersonsT2;
                    } else {
                        $personsByRoundTripT1 += $newPersonsT1;
                        $personsByRoundTripT2 += $newPersonsT2;
                    }

                    $totalPersonsT1 += $newPersonsT1;
                    $totalPersonsT2 += $newPersonsT2;

                    // Procesa conteos en rutas largas, tomando en cuenta que en cada PdC hay liberación de asientos
                    $prevTotalActivationCounts = $activationCounts->count();
                    $seatingActivated->pluck('number')->map(function ($seat) use (&$activationCounts) {
                        $activationCounts->put($seat, intval($activationCounts->get($seat)) + 1);
                    });

                    $personsByRoundTripActivationCounts = $activationCounts->count();
                    $totalPersonsActivationCounts += $personsByRoundTripActivationCounts - $prevTotalActivationCounts;
                }

                $this->processLockCam($photo, $counterLock, $alertLockCam);

                $details = $this->getPhotoDetails($photo, $currentOccupation, $seatingActivated, $seatingReleased);

                $rekognitionCounts = $this->processRekognitionCounts($details, $prevDetails, $pevRekognitionCounts, $photo, $statusDR->start);

                $criteriaCount = 'max';
                $criteriaCount = 'averages';
                $criteriaCount = 'topologies';

                if ($statusDR->isActive() && $statusDR->dr->route->isLarge()) {
                    $criteriaCount = 'seatingActivationCounts';
                }

//                $totalPersonsT = max($totalPersonsT1, $totalPersonsT2);
//                $personsByRoundTripT = max($personsByRoundTripT1, $personsByRoundTripT2);

                $totalPersonsT = $totalPersonsT1;
                $personsByRoundTripT = $personsByRoundTripT1;

                switch ($criteriaCount) {
                    case 'averages':
                        ###### AVERAGES BETWEEN TOPOLOGIES AND FACES MAX CRITERIA
                        $maximumCriteria = $rekognitionCounts->get('faces');
                        $personsByRoundTrip = ceil((($personsByRoundTripT ?: $maximumCriteria->max->value) + $maximumCriteria->max->value) / 2);
                        $totalPersons = ceil((($totalPersonsT1 ?: $maximumCriteria->total) + $maximumCriteria->total) / 2);
                        break;
                    case 'max':
                        ###### FACES MAX CRITERIA
                        $maximumCriteria = $rekognitionCounts->get('faces');
                        $personsByRoundTrip = $maximumCriteria->max->value;
                        $totalPersons = $maximumCriteria->total;
                        break;
                    case 'seatingActivationCounts':
                        ###### CRITERIO PARA CONTEOS DE ASIENTOS ACTIVADOS DURANTE TODO EL TRAYECTO
                        $personsByRoundTrip = $personsByRoundTripActivationCounts;
                        $totalPersons = $totalPersonsActivationCounts;
                        $maxPersonByRoundTrip = $rekognitionCounts->get('faces')->max->value;
                        break;
                    case 'topologies':
                    default:
                        #### Default Count by topologies
                        $personsByRoundTrip = $personsByRoundTripT;
                        $totalPersons = $totalPersonsT;
                        $maxPersonByRoundTrip = $rekognitionCounts->get('faces')->max->value;
                        break;
                }

                $personsByRoundTrips = collect([])->push((object)[
                    'id' => $statusDR->getDRId(),
                    'number' => $statusDR->getRoundTrip(),
                    'route' => $statusDR->getRouteName(),
                    'from' => $statusDR->getFrom(),
                    'to' => $statusDR->getTo(),
                    'persons' => $personsByRoundTrip,
                    'count' => $personsByRoundTrip,
                ]);

                $historic->push((object)[
                    'id' => $photo->id,
                    'camera' => $photo->side,
                    'ts' => $photo->date->timestamp,
                    'time' => $photo->date->format('H:i:s.u') . '' . $photo->id,
                    'drId' => $statusDR->getDRId(),
                    'drStatus' => $statusDR->text,
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
                        'maxPersonByRoundTrip' => $maxPersonByRoundTrip,
                        'seating' => $activationCounts
                    ],
                ]);

                $prevPhoto = $photo;
                $prevOccupation = $currentOccupation;
                $prevDetails = $details;

                $pevRekognitionCounts = $rekognitionCounts;
            }
        }

        return $historic;
    }

    function getPhotoDetails(Photo $photo, $currentOccupation, $seatingActivated, $seatingReleased)
    {
        $details = $photo->getAPIFields('url');
        $details->occupation = $currentOccupation;
        $details->occupation->seatingOccupiedStr = $details->occupation->seatingOccupied->keys()->sort()->implode(', ');
        $details->occupation->seatingBoardingStr = $this->getBoardingSeating($details->occupation->seatingOccupied, $prevDetails->occupation->seatingOccupied ?? [])->implode(', ');
        $details->occupation->seatingMixStr = $currentOccupation->withOverlap ? $currentOccupation->seatingOccupied->keys()->sort()->implode(', ') : "";
        $details->occupation->seatingReleaseStr = $seatingReleased->keys()->sort()->implode(', ');
        $details->occupation->seatingActivatedStr = $seatingActivated->keys()->sort()->implode(', ');

        return $details;
    }


    function processRekognitionCounts($details, $prevDetails, $pevRekognitionCounts = null, Photo $photo, $firstPhotoInRoundTrip)
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

        if (isset($image_parts[1])) {
            return base64_decode($image_parts[1]);
        }

        return $image_parts[0];
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

    function getElapsed(Carbon $from)
    {
        return Carbon::now()->diffInSeconds($from);
    }
}

