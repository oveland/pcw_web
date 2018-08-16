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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationReport whereVehicleStatusId($value)
 * @property-read \App\Vehicle|null $vehicle
 */
class CurrentLocationReport extends Model
{
    use LocationReportTrait;
}
