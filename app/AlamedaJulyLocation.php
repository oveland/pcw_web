<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AlamedaJulyLocation
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
 * @property-read \App\AlamedaJulyReport $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJulyLocation whereVersion($value)
 * @mixin \Eloquent
 */
class AlamedaJulyLocation extends Model
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
        return $this->hasOne(AlamedaJulyReport::class,'location_id','id');
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
