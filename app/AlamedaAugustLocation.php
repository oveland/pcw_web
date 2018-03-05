<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AlamedaAugustLocation
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
 * @property-read \App\AlamedaAugustReport $report
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AlamedaAugustLocation whereVersion($value)
 * @mixin \Eloquent
 */
class AlamedaAugustLocation extends Model
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
        return $this->hasOne(AlamedaAugustReport::class,'location_id','id');
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
