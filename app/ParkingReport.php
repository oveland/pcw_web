<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ParkingReport
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ParkingReport whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\ControlPoint|null $controlPoint
 * @property-read \App\DispatchRegister|null $dispatchRegister
 * @property-read \App\Vehicle $vehicle
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
