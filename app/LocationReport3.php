<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LocationReport3
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereLocationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereTimed($value)
 * @mixin \Eloquent
 * @property int|null $vehicle_id
 * @property float|null $orientation
 * @property int|null $vehicle_status_id
 * @property int|null $speed
 * @property float|null $distance
 * @property float|null $odometer
 * @property int|null $report_id
 * @property string|null $status
 * @property int|null $control_point_id
 * @property int|null $fringe_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport3 whereVehicleStatusId($value)
 */
class LocationReport3 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_3';
}
