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
}
