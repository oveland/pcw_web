<?php

namespace App;

use Carbon\Carbon;
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
 * @property int|null $speed
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Speeding whereSpeed($value)
 */
class Speeding extends Model
{
    protected $table = 'speeding';

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getTimeAttribute($time)
    {
        return Carbon::createFromFormat(config('app.simple_time_format'),explode('.',$time)[0]);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getSpeedAttribute($speed)
    {
        $thresholdTruncateSpeeding = config('vehicle.threshold_truncate_speeding');
        return ($speed > $thresholdTruncateSpeeding) ? $thresholdTruncateSpeeding : $speed;
    }

    public function isTruncated()
    {
        $speed = $this->speed;
        $thresholdTruncateSpeeding = config('vehicle.threshold_truncate_speeding');
        return ($speed > $thresholdTruncateSpeeding);
    }
}
