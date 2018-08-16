<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrentSensorPassengers extends Model
{
    protected $table = 'contador';

    public function getPassengersAttribute()
    {
        return $this->pas_tot;
    }

    public function getTimeStatusAttribute()
    {
        return $this->hora_status;
    }

    public function getTimeSensorRecorderAttribute()
    {
        return $this->time_change_sensor_recorder;
    }
}
