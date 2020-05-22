<?php

namespace App\Models\Apps\Rocket;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use File;
use Image;
use Storage;

/**
 * App\Models\Apps\Rocket\Photo
 *
 * @method static Builder|Photo newModelQuery()
 * @method static Builder|Photo newQuery()
 * @method static Builder|Photo query()
 * @mixin Eloquent
 * @property int $id
 * @property Carbon $date
 * @property int $vehicle_id
 * @property int $dispatch_register_id
 * @property int $location_id
 * @property string $path
 * @property string $side
 * @property string $type
 * @property string $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Photo whereCreatedAt($value)
 * @method static Builder|Photo whereDate($value)
 * @method static Builder|Photo whereData($value)
 * @method static Builder|Photo whereDispatchRegisterId($value)
 * @method static Builder|Photo whereId($value)
 * @method static Builder|Photo whereLocationId($value)
 * @method static Builder|Photo wherePath($value)
 * @method static Builder|Photo whereSide($value)
 * @method static Builder|Photo whereType($value)
 * @method static Builder|Photo whereUpdatedAt($value)
 * @method static Builder|Photo whereVehicleId($value)
 * @property-read Vehicle $vehicle
 * @method static Builder|CurrentPhoto findByVehicle(Vehicle $vehicle)
 */
class CurrentPhoto extends Model
{
    protected $table = 'app_current_photos';

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getDateAttribute($date)
    {
        if (Str::contains($date, '-')) {
            return $date = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        }

        return Carbon::createFromFormat($this->getDateFormat(), explode('.', $date)[0]);
    }

    protected $fillable = ['date', 'vehicle_id', 'dispatch_register_id', 'location_id', 'path', 'type', 'data', 'side'];

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
     * @param Builder $query
     * @param Vehicle $vehicle
     * @return Builder
     */
    function scopeFindByVehicle(Builder $query, Vehicle $vehicle)
    {
        $currentPhoto = $query->where('vehicle_id', $vehicle->id)->first();
        $currentPhoto = $currentPhoto ? $currentPhoto : new CurrentPhoto();
        $currentPhoto->vehicle()->associate($vehicle);
        return $currentPhoto;
    }

    public function getAPIFields()
    {
        $dispatchRegister = $this->dispatchRegister;

        return (object)[
            'date' => $this->date->toDateTimeString(),
            'side' => $this->side,
            'type' => Str::ucfirst(__($this->type)),
            'vehicle_id' => $this->vehicle_id,
            'dispatchRegister' => $dispatchRegister ? $dispatchRegister->getAPIFields() : null,
            'data' => json_decode($this->side ?? "{}"),
        ];
    }

    /**
     * @param string $encode
     * @return \Intervention\Image\Image
     */
    public function encode($encode = "webp")
    {
        if($encode == "url"){
//            return config('app.url')."/api/v2/files/rocket/get-photo?id=$this->id";
        }

        if ($this->vehicle && Storage::exists($this->path)) {
            return Image::make(Storage::get($this->path))->encode($encode);
        } else {
            return Image::make(File::get('img/image-404.jpg'))->resize(300,300)->encode($encode);
        }
    }
}
