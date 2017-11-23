<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ControlPointsReport
 *
 * @property-read \App\ControlPoint $controlPoint
 * @property-read \App\DispatchRegister $dispatchRegister
 * @property-read \App\Fringe $fringe
 * @property-read \App\Location $location
 * @property-read \App\Vehicle $vehicle
 * @mixin \Eloquent
 * @property int $id
 * @property int $version
 * @property int|null $control_point_id
 * @property string $date
 * @property string $date_created
 * @property int $dispatch_register_id
 * @property int $distanced
 * @property int $distancem
 * @property int $distancep
 * @property int $fringe_id
 * @property string $last_updated
 * @property int $location_id
 * @property string $status
 * @property float $status_in_minutes
 * @property string $timed
 * @property string $timem
 * @property string $timep
 * @property int $vehicle_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTimeReport whereVersion($value)
 */
class ControlPointTimeReport extends Model
{
    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    function controlPoint(){
        return $this->belongsTo(ControlPoint::class);
    }

    function dispatchRegister(){
        return $this->belongsTo(DispatchRegister::class);
    }

    function fringe(){
        return $this->belongsTo(Fringe::class);
    }

    function location(){
        return $this->belongsTo(Location::class);
    }

    function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }
}
