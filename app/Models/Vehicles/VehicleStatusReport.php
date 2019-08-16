<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\VehicleStatusReport
 *
 * @property int $id
 * @property string $date
 * @property string $time
 * @property int|null $vehicle_id
 * @property int|null $vehicle_status_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $orientation
 * @property float|null $speed
 * @property int|null $odometer
 * @property string|null $frame
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereVehicleStatusId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Vehicles\VehicleStatus|null $status
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @property int|null $dispatch_register_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleStatusReport query()
 */
class VehicleStatusReport extends Model
{
    protected $dates = ['date'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat( config('app.date_format'), $date );
    }

    public function status()
    {
        return $this->belongsTo(VehicleStatus::class,'vehicle_status_id','id_status');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
