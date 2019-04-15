<?php

namespace App\Models\Vehicles;

use App\Models\Routes\DispatchRegister;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\Speeding
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $time
 * @property int|null $vehicle_id
 * @property int|null $speeed
 * @property float|null $latitude
 * @property float|null $longitude
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereSpeeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereVehicleId($value)
 * @mixin \Eloquent
 * @property int|null $speed
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereSpeed($value)
 * @property int|null $dispatch_register_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Speeding whereDispatchRegisterId($value)
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 */
class Speeding extends Model
{
    protected $table = 'speeding';

    function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getTimeAttribute($time)
    {
        return Carbon::createFromFormat(config('app.simple_time_format'),explode('.',$time)[0]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
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
