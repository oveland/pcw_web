<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\CurrentParkingReport
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDistanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDistancem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereDistancep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereStatusInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereTimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereTimem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereTimep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport whereVehicleId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentParkingReport findByVehicleId($vehicle_id)
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
