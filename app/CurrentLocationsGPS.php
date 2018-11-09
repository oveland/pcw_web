<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\CurrentLocationsGPS
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
 * @property-read \App\VehicleStatus|null $vehicleStatus
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS findByVehicleId($vehicleId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereDateVehicleStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereVehiclePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereVehicleStatusId($value)
 * @mixin \Eloquent
 * @property string|null $time_period
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentLocationsGPS whereTimePeriod($value)
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
        return $query->where('vehicle_id', $vehicleId)->get()->first() ?? null;
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }
}
