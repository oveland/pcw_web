<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrentLocationsGPS extends Model
{
    protected $table = 'current_locations_gps';

    public function scopeFindByVehicleId($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId)->get()->first() ?? null;
    }

    public function vehicleStatus()
    {
        return $this->belongsTo(VehicleStatus::class, 'vehicle_status_id', 'id_status');
    }
}
