<?php


namespace App\Models\Apps\Rocket\Traits;


use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait PhotoGlobals
{
    use PhotoEncode;

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

    /**
     * @return BelongsTo | DispatchRegister
     */
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    /**
     * @param string $encodeImage
     * @return object
     */
    public function getAPIFields($encodeImage = 'url')
    {
        $dispatchRegister = $this->dispatchRegister;

        return (object)[
            'id' => $this->id,
            'url' => $this->encode($encodeImage),
            'path' => $this->getOriginalPath(),
            'date' => $this->date->toDateTimeString(),
            'side' => Str::ucfirst(__($this->side)),
            'type' => Str::ucfirst(__($this->type)),
            'vehicle_id' => $this->vehicle_id,
            'dispatchRegister' => $dispatchRegister ? $dispatchRegister->getAPIFields() : null,
            'persons' => $this->data,
            'occupation' => null
        ];
    }

    public function setDataAttribute($data)
    {
        $this->attributes['data'] = collect($data)->toJson();
    }

    public function setEffectsAttribute($effects)
    {
        $this->attributes['effects'] = collect($effects)->toJson();
    }

    /**
     * @param $data
     * @return object
     */
    function getDataAttribute($data)
    {
        return $data && Str::of($data)->startsWith('{') && Str::of($data)->endsWith('}') ? (object)json_decode($data, true) : null;
    }

    /**
     * @param $effects
     * @return object
     */
    function getEffectsAttribute($effects)
    {
        return $effects && Str::of($effects)->startsWith('{') && Str::of($effects)->endsWith('}') ? (object)json_decode($effects, true) : null;
    }
}