<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proprietary extends Model
{
    public function fullName()
    {
        return "$this->first_name $this->second_name $this->surname $this->second_surname";
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedVehicles()
    {
        return $this->hasMany(ProprietaryVehicle::class);
    }
}
