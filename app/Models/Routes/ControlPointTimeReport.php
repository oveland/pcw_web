<?php

namespace App\Models\Routes;

use App\Models\Vehicles\Location;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\ControlPointTimeReport
 *
 * @property-read \App\Models\Routes\ControlPoint $controlPoint
 * @property-read \App\Models\Routes\DispatchRegister $dispatchRegister
 * @property-read \App\Models\Routes\Fringe $fringe
 * @property-read \App\Models\Vehicles\Location $location
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTimeReport whereVersion($value)
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

    public function fast(){
        return $this->status == 'fast';
    }

    public function slow(){
        return $this->status == 'slow';
    }
}
