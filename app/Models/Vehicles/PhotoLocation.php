<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereDataFaces($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereDataPersons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereDataProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereEffects($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation wherePersons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereRekognition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereSide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\PhotoLocation whereVehicleId($value)
 * @mixin \Eloquent
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
        ];
    }
}
