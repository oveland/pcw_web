<?php

namespace App\Models\Apps\Rocket;

use App\Models\Apps\Rocket\Traits\PhotoEncode;
use App\Models\Apps\Rocket\Traits\PhotoGlobals;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
 * @property int|null $persons
 * @property-read DispatchRegister|null $dispatchRegister
 * @method static Builder|CurrentPhoto wherePersons($value)
 * @property string $disk
 * @property string|null $effects
 * @method static Builder|CurrentPhoto whereDisk($value)
 * @method static Builder|CurrentPhoto whereEffects($value)
 * @property string $rekognition
 * @method static Builder|Photo whereRekognition($value)
 * @property string|null $data_persons
 * @method static Builder|Photo whereDataPersons($value)
 * @property string|null $data_faces
 * @method static Builder|Photo whereDataFaces($value)
 * @property string|null $data_properties
 * @method static Builder|CurrentPhoto whereDataProperties($value)
 */
class CurrentPhoto extends Model implements PhotoInterface
{
    public const BASE_PATH = '/Apps/Rocket/Photos/';

    use PhotoGlobals, PhotoEncode;

    protected $table = 'app_current_photos';
    protected $fillable = ['date', 'vehicle_id', 'dispatch_register_id', 'location_id', 'path', 'type', 'data', 'side', 'disk'];

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
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
        $currentPhoto->date = Carbon::now();
//        $currentPhoto->save();
        return $currentPhoto;
    }
}
