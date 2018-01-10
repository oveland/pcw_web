<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LocationReport5
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereLocationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport5 whereTimed($value)
 * @mixin \Eloquent
 */
class LocationReport5 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_5';
}
