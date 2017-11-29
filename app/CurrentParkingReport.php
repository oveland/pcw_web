<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CurrentParkingReport
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentParkingReport whereVehicleId($value)
 * @mixin \Eloquent
 */
class CurrentParkingReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['*'];

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function scopeFindByVehicleId($query, $vehicle_id)
    {
        return $query->where('vehicle_id', $vehicle_id)->limit(1)->get();
    }
}
