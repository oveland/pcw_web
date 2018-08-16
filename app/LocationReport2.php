<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LocationReport2
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereLocationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereTimed($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LocationReport2 whereVehicleStatusId($value)
 * @property-read \App\Vehicle|null $vehicle
 */
class LocationReport2 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_2';
}
