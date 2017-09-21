<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AlamedaJuneLocation
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
 * @property-read \App\AlamedaJuneReport $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaJuneLocation whereVersion($value)
 * @mixin \Eloquent
 */
class AlamedaJuneLocation extends Model
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
        return $this->hasOne(AlamedaJuneReport::class,'location_id','id');
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
