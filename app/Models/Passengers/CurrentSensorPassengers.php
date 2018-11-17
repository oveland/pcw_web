<?php

namespace App\Models\Passengers;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CurrentSensorPassengers
 * @package App
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\CurrentSensorPassengers whereVehicle($vehicle)
 */
class CurrentSensorPassengers extends Model
{
    protected $table = 'contador'; // TODO Change table name when table contador is migrated

    public function getPassengersAttribute()
    {
        return $this->pas_tot;
    }

    public function getTimeStatusAttribute()
    {
        return explode('.', $this->hora_status)[0];
    }

    public function getTimeSensorAttribute()
    {
        return $this->timeStatus;
    }

    public function getTimeSensorRecorderAttribute()
    {
        return explode('.', $this->time_change_sensor_recorder)[0];
    }

    public function getSeatingAttribute()
    {
        return $this->asientos;
    }

    public function scopeWhereVehicle($query, $vehicle)
    {
        return $query->where('placa', $vehicle ? $vehicle->plate : '')->get()->first();
    }

    public function getSensorCounterAttribute()
    {
        return $this->pas_tot;
    }

    public function getSensorRecorderCounterAttribute()
    {
        return $this->des_p1;
    }
}
