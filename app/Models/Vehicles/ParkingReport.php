<?php

namespace App\Models\Vehicles;

use App\Models\Routes\ControlPoint;
use App\Models\Routes\DispatchRegister;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\ParkingReport
 *
 * @property int $id
 * @property string $date
 * @property int|null $location_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $orientation
 * @property int|null $speed
 * @property float|null $odometer
 * @property int|null $report_id
 * @property int|null $dispatch_register_id
 * @property int|null $distancem
 * @property int|null $distancep
 * @property int|null $distanced
 * @property string|null $timem
 * @property string|null $timep
 * @property string|null $timed
 * @property float|null $status_in_minutes
 * @property float|null $control_point_id
 * @property float|null $fringe_id
 * @property int $vehicle_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\ParkingReport whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\ControlPoint|null $controlPoint
 * @property-read \App\Models\Routes\DispatchRegister|null $dispatchRegister
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 */
class ParkingReport extends Model
{
    protected $dates = ['date'];

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    public function controlPoint()
    {
        return $this->belongsTo(ControlPoint::class);
    }
}
