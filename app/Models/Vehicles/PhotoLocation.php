<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\PhotoLocation
 *
 * @property int $id
 * @property Carbon | string $date
 * @property int $vehicle_id
 * @property int|null $dispatch_register_id
 * @property int|null $location_id
 * @property string $path
 * @property string $side
 * @property string $type
 * @property string|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $persons
 * @property string $disk
 * @property string|null $effects
 * @property string $rekognition
 * @property string|null $data_persons
 * @property string|null $data_faces
 * @property string|null $data_properties
 * @property int|null $uid
 * @method static Builder|PhotoLocation whereCreatedAt($value)
 * @method static Builder|PhotoLocation whereData($value)
 * @method static Builder|PhotoLocation whereDataFaces($value)
 * @method static Builder|PhotoLocation whereDataPersons($value)
 * @method static Builder|PhotoLocation whereDataProperties($value)
 * @method static Builder|PhotoLocation whereDate($value)
 * @method static Builder|PhotoLocation whereDisk($value)
 * @method static Builder|PhotoLocation whereDispatchRegisterId($value)
 * @method static Builder|PhotoLocation whereEffects($value)
 * @method static Builder|PhotoLocation whereId($value)
 * @method static Builder|PhotoLocation whereLocationId($value)
 * @method static Builder|PhotoLocation wherePath($value)
 * @method static Builder|PhotoLocation wherePersons($value)
 * @method static Builder|PhotoLocation whereRekognition($value)
 * @method static Builder|PhotoLocation whereSide($value)
 * @method static Builder|PhotoLocation whereType($value)
 * @method static Builder|PhotoLocation whereUid($value)
 * @method static Builder|PhotoLocation whereUpdatedAt($value)
 * @method static Builder|PhotoLocation whereVehicleId($value)
 * @mixin Eloquent
 */
class PhotoLocation extends Model
{
    protected $table = "app_photos";

    protected $dates = ['date'];

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'side'=> $this->side
        ];
    }
}
