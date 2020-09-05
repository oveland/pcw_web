<?php

namespace App\Models\Apps\Rocket;

use App\Models\Apps\Rocket\Traits\PhotoEncode;
use App\Models\Apps\Rocket\Traits\PhotoGlobals;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Models\Apps\Rocket\Traits\PhotoRekognition;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
 * @method static Builder|Photo whereDate($value)
 * @property int|null $persons
 * @property-read DispatchRegister|null $dispatchRegister
 * @method static Builder|Photo wherePersons($value)
 * @property object $effects
 * @method static Builder|Photo whereEffects($value)
 * @property string $disk
 * @method static Builder|Photo whereDisk($value)
 * @method static Collection|Photo[] findAllByVehicleAndDate(Vehicle $vehicle, $date)
 * @property string $rekognition
 * @method static Builder|Photo whereRekognition($value)
 * @property string|null $data_persons
 * @method static Builder|Photo whereDataPersons($value)
 * @property string|null $data_faces
 * @method static Builder|Photo whereDataFaces($value)
 * @property string|null $data_properties
 * @method static Builder|Photo whereDataProperties($value)
 * @property string|null $uid
 * @method static Builder|Photo whereUid($value)
 */
class Photo extends Model implements PhotoInterface
{
    use PhotoRekognition;
    use PhotoGlobals, PhotoEncode;

    public const BASE_PATH = '/Apps/Rocket/Photos/';

    protected $table = 'app_photos';
    protected $fillable = ['date', 'vehicle_id', 'dispatch_register_id', 'location_id', 'path', 'type', 'data', 'side', 'disk', 'uid'];

    protected static function boot()
    {
        parent::boot();
        static::saving(function (Photo $photo) {
            $photo->processRekognition($photo->rekognition);
        });
    }

    /**
     * @param Builder $query
     * @param Vehicle $vehicle
     * @param $date
     * @return Photo[]|Collection
     */
    function scopeFindAllByVehicleAndDate(Builder $query, Vehicle $vehicle, $date)
    {
        return $query
            ->where('vehicle_id', $vehicle->id)
            ->whereDate('date', $date)
            ->orderBy('date')


            //            1 DE SEPTEMBER

            ->whereBetween('date', ['2020-09-01 05:50:00', '2020-09-01 6:54:59'])

            ->limit(600)
            ->get();
    }
}
