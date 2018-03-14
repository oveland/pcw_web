<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LocationReport0
 *
 * @property int|null $location_id
 * @property int|null $dispatch_register_id
 * @property bool|null $off_road
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $date
 * @property string|null $location_date
 * @property string|null $timed
 * @property int|null $distancem
 * @property float|null $status_in_minutes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereLocationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport0 whereTimed($value)
 * @mixin \Eloquent
 */
class CurrentLocationReport extends Model
{
    use LocationReportTrait;
}
