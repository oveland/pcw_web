<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LocationReport1
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereLocationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport1 whereTimed($value)
 * @mixin \Eloquent
 */
class LocationReport1 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_1';
}