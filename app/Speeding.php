<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Speeding
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property int|null $vehicle_id
 * @property int|null $speeed
 * @property float|null $latitude
 * @property float|null $longitude
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Speeding whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Speeding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Speeding whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Speeding whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Speeding whereSpeeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Speeding whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Speeding whereVehicleId($value)
 * @mixin \Eloquent
 */
class Speeding extends Model
{
    protected $table = 'speeding';

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
