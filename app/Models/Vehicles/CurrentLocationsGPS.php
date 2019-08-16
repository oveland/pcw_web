<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\CurrentLocationsGPS
 *
 * @property int $id
 * @property string|null $date
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $orientation
 * @property float|null $speed
 * @property int|null $vehicle_status_id
 * @property string|null $date_vehicle_status
 * @property int|null $vehicle_id
 * @property string|null $vehicle_plate
 * @property-read \App\Models\Vehicles\VehicleStatus|null $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS findByVehicleId($vehicleId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereDateVehicleStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereVehiclePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereVehicleStatusId($value)
 * @mixin \Eloquent
 * @property string|null $time_period
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS whereTimePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\CurrentLocationsGPS query()
 */
class CurrentLocationsGPS extends Model
{
    protected $table = 'current_locations_gps';

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    public function getTimePeriod()
    {
        return explode('.',$this->time_period)[0];
    }

    public function scopeFindByVehicleId($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }
}
