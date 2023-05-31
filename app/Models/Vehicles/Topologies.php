<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

class Topologies extends Model
{
    protected $table="topologies_seats";
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
