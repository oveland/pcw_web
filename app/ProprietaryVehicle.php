<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProprietaryVehicle extends Model
{
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function proprietary()
    {
        return $this->belongsTo(Proprietary::class);
    }
}
