<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\OffRoad
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property \Carbon\Carbon|null $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property \Carbon\Carbon|null $last_updated
 * @property string|null $latitude
 * @property string|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property mixed $dispatch_register
 * @property-read \App\Report $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad whereVersion($value)
 * @mixin \Eloquent
 * @property-read \App\DispatchRegister|null $dispatchRegister
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OffRoad validCoordinates()
 */
class OffRoad extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'),explode('.',$date)[0]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report()
    {
        return $this->hasOne(Report::class, 'location_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    /**
     * Check valid coordinates
     *
     * @return bool
     */
    public function isValid()
    {
        return ($this->latitude != 0 && $this->longitude != 0) ? true : false;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeValidCoordinates($query)
    {
        return $query->where('latitude', '<>', 0)->where('longitude', '<>', 0);
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
