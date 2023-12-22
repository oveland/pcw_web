<?php


namespace App\Models\Apps\Rocket\Traits;


use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\Photos\PhotoRekognitionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App;

trait PhotoGlobals
{
    public $cp = null;
    
    /**
     * @param $type
     * @return PhotoRekognitionService
     */
    public function photoRekognitionService($type)
    {
        $profileSeating = $this->vehicle->getProfileSeating($this->side);
        $configProfile = $this->vehicle->getConfigProfile($this->side, $this->date, $profileSeating);

        return App::make("rocket.photo.rekognition.$type", ['profileSeating' => $profileSeating, 'configProfile' => $configProfile]);
    }

    public function getDateAttribute($date)
    {
        if (Str::contains($date, '-')) {
            return $date = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        }

        return Carbon::createFromFormat($this->getDateFormat(), explode('.', $date)[0]);
    }

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return BelongsTo | DispatchRegister
     */
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    /**
     * @param Builder $query
     * @param $uid
     * @return Builder
     */
    public function scopeWhereUid(Builder $query, $uid)
    {
        return $query->where('uid', $uid);
    }

    /**
     * @param string $encodeImage
     * @param bool $withEffects
     * @param false $withMask
     * @return object
     */
    public function getAPIFields($encodeImage = 'url', $withEffects = true, $withMask = false)
    {
        $dispatchRegister = $this->dispatchRegister;
        $location = Location::select(['distance', 'date'])->where('id', $this->location_id)->first();
        if ($dispatchRegister) {
            $dispatchRegister = (object)[
                'id' => $dispatchRegister->id,
                'turn' => $dispatchRegister->turn,
                'round_trip' => $dispatchRegister->round_trip,
                'roundTrip' => $dispatchRegister->round_trip,
                'departure_time' => $dispatchRegister->departure_time,
                'date_end' => $dispatchRegister->date_end,
                'arrival_time' => $dispatchRegister->arrival_time,
                'route' => (object)[
                    'id' => $dispatchRegister->route_id,
                    'name' => $dispatchRegister->route->name . ($location ? " • $location->distance" : ''),
                    'distance' => $dispatchRegister->route->distance_in_meters,
                ],
                'distance' => $dispatchRegister->getRouteDistance(),
            ];
        }

        return (object)[
            'id' => $this->id,
            'url' => $this->encode($encodeImage, $withEffects, $withMask),
            'path' => $this->getOriginalPath(),
            'date' => $this->date->toDateTimeString(),
            'side' => Str::ucfirst(__($this->side)),
            'type' => Str::ucfirst(__($this->type)),
            'vehicle_id' => $this->vehicle_id,
            'dispatchRegister' => $dispatchRegister,
            'persons' => $this->data,
            'location_id' => $this->location_id,
            'occupation' => null
        ];
    }

    public function setDataAttribute($dataPersons)
    {
        // TODO Change implementation of property ->data to ->data_persons and release data column
        $this->setDataPersonsAttribute($dataPersons);
        $this->attributes['data'] = $this->attributes['data_persons'];
    }

    /**
     * @param $dataPersons
     * @return object
     */
    function getDataAttribute($dataPersons)
    {
        // TODO Change implementation of property ->data to ->data_persons and release data column
        return $this->data_persons;
    }

    public function setDataPersonsAttribute($dataPersons)
    {
        $dataPersons = $this->photoRekognitionService('persons')->processRekognition($dataPersons);
        $this->attributes['data_persons'] = collect($dataPersons)->toJson(JSON_FORCE_OBJECT);
    }

    /**
     * @param $dataPersons
     * @return object
     */
    function getDataPersonsAttribute($dataPersons)
    {
        $dataPersons = $dataPersons && Str::of($dataPersons)->startsWith('{') && Str::of($dataPersons)->endsWith('}') ? (object)json_decode($dataPersons, true) : null;
        if ($dataPersons) {
            return $this->photoRekognitionService('persons')->processRekognition($dataPersons);
        }

        return null;
    }


    public function setDataFacesAttribute($dataFaces)
    {
        $dataFaces = $this->photoRekognitionService('faces')->processRekognition($dataFaces);
        $this->attributes['data_faces'] = collect($dataFaces)->toJson(JSON_FORCE_OBJECT);
    }

    /**
     * @param $dataFaces
     * @return object
     */
    function getDataFacesAttribute($dataFaces)
    {
        $dataFaces = $dataFaces && Str::of($dataFaces)->startsWith('{') && Str::of($dataFaces)->endsWith('}') ? (object)json_decode($dataFaces, true) : null;
        if ($dataFaces) {
            return $this->photoRekognitionService('faces')->processRekognition($dataFaces);
        }

        return null;
    }

    public function setEffectsAttribute($effects)
    {
        $this->attributes['effects'] = collect($effects)->toJson();
    }

    /**
     * @param $effects
     * @return object
     */
    function getEffectsAttribute($effects)
    {
        $ef = $effects && Str::of($effects)->startsWith('{') && Str::of($effects)->endsWith('}') ? (object)json_decode($effects, true) : null;

        if (!$ef || !is_array($ef->brightness)) {
            $config = $this->photoRekognitionService('faces')->config;
            $this->effects = $config->photo->effects;
        }

        return $ef;
    }

    public function setDataPropertiesAttribute($dataProperties)
    {
        $this->attributes['data_properties'] = collect($dataProperties)->toJson();
    }

    /**
     * @param $dataProperties
     * @return object
     */
    function getDataPropertiesAttribute($dataProperties)
    {
        return $dataProperties && Str::of($dataProperties)->startsWith('{') && Str::of($dataProperties)->endsWith('}') ? (object)json_decode($dataProperties, true) : (object)[];
    }
}