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
 * @method static Builder|Photo whereDate($column, $value)
 * @property int|null $persons
 * @property-read DispatchRegister|null $dispatchRegister
 * @method static Builder|Photo wherePersons($value)
 * @property object $effects
 * @method static Builder|Photo whereEffects($value)
 * @property string $disk
 * @method static Builder|Photo whereDisk($value)
 * @method static Builder|Photo whereVehicleAndDate(Vehicle $vehicle, $date)
 * @method static Builder|Photo whereVehicleAndDateAndSide(Vehicle $vehicle, $date, $side)
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
    protected $hidden = ['location'];

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
     * @param Builder|Photo $query
     * @param Vehicle $vehicle
     * @param $date
     * @param $side
     * @return Builder
     */
    function scopeWhereVehicleAndDateAndSide(Builder $query, Vehicle $vehicle, $date, $side)
    {
        $side = "$side";
        $query = $query->whereVehicleAndDate($vehicle, $date);

        if ($side !== null && $side != 'all' && $side !== "") {
            return $query->where('side', $side);
        }
        return $query;
    }

    /**
     * @param Builder $query
     * @param Vehicle $vehicle
     * @param $date
     * @return Builder
     */
    function scopeWhereVehicleAndDate(Builder $query, Vehicle $vehicle, $date)
    {
        return $query
//            ->whereBetween('date', ["$date 00:00:00", "$date 23:59:59"])
            ->whereDate('date', $date)
            ->where('vehicle_id', $vehicle->id)
            ->orderBy('date')

            //->where('id' , '>', 44452) // Empty

//            ->where('id', 44354) // Empty
//            ->where('id', 44422) // Empty

//            ->where('id', 44436) // Detect 5
//            ->where('id', 44440) // Detect 2
//            ->where('id', 44425) // Detect 5
//            ->where('id', 44427) // Detect 3
//            ->where('id', 44430) // Detect 2
//            ->where('id', 44402) // Detect X

//            ->whereBetween('date', ['2020-09-02 14:50:00', '2020-09-02 16:54:59'])


            // ALAMEDA October 1st

//            ->where('dispatch_register_id', 1275379) // Round trip 1
//            ->where('dispatch_register_id', 1275564) // Round trip 2
//            ->where('dispatch_register_id', 1275837) // Round trip 3
//            ->where('dispatch_register_id', 1276144) // Round trip 4
//            ->where('dispatch_register_id', 1276410) // Round trip 5

            // ALAMEDA October 2nd
//            ->where('dispatch_register_id', 1276931) // Round trip 1
//            ->where('dispatch_register_id', 1277126) // Round trip 2
//            ->where('dispatch_register_id', 1277408) // Round trip 3
//            ->where('dispatch_register_id', 1277691) // Round trip 4
//            ->where('dispatch_register_id', 1278007) // Round trip 5


            // ALAMEDA October 3rd
//            ->where('dispatch_register_id', 1278537) // Round trip 1
//            ->where('dispatch_register_id', 1278662) // Round trip 2
//            ->where('dispatch_register_id', 1278867) // Round trip 3
//            ->where('dispatch_register_id', 1279122) // Round trip 4
//            ->where('dispatch_register_id', 1279335) // Round trip 5


            // ALAMEDA October 5th
//            ->where('dispatch_register_id', 1280724) // Round trip 1
//            ->where('dispatch_register_id', 1280886) // Round trip 2
//            ->where('dispatch_register_id', 1281187) // Round trip 3
//            ->where('dispatch_register_id', 1281544) // Round trip 4

//                ->whereBetween('id', [47683, 47686])

//            ->limit(30)->offset(220)
            ->limit(600)
            ->with(['dispatchRegister', 'vehicle']);
    }
}
