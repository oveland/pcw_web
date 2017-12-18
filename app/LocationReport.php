<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LocationReport
 *
 * @property int|null $location_id
 * @property int|null $dispatch_register_id
 * @property bool|null $off_road
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $date
 * @property string|null $timed
 * @property int|null $distancem
 * @property float|null $status_in_minutes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereTimed($value)
 * @mixin \Eloquent
 * @property string|null $location_date
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport whereLocationDate($value)
 */
class LocationReport extends Model
{
    use LocationReportTrait;
}
