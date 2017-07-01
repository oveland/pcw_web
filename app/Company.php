<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class,'company_id','id');
    }

    public function activeVehicles()
    {
        return $this->hasMany(Vehicle::class,'company_id','id')->where('active',true);
    }
}
