<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LocationReport6
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereLocationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereTimed($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport6 whereVehicleStatusId($value)
 * @property-read \App\Vehicle|null $vehicle
 */
class LocationReport6 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_6';
}
