<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Location
 *
 * @property int $id
 * @property int $version
 * @property string|null $date
 * @property \Carbon\Carbon $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property \Carbon\Carbon $last_updated
 * @property string|null $latitude
 * @property string|null $longitude
 * @property float|null $odometer
 * @property float|null $orientation
 * @property int|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property bool|null $off_road
 * @property-read \App\Report $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereVersion($value)
 * @mixin \Eloquent
 */
class Location extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report()
    {
        return $this->hasOne(Report::class,'location_id','id');
    }

    /**
     * Check valid coordinates
     *
     * @return bool
     */
    public function isValid()
    {
        return ($this->latitude != 0 && $this->longitude != 0)?true:false;
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
