<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int $id
 * @property \Carbon\Carbon $updated_at
 */
class SimGPS extends Model
{
    protected $table = 'sim_gps';

    protected $fillable = ['sim','operator','gps_type','vehicle_id','active'];

    public function scopeFindByVehicleId($query, $vehicle_id)
    {
        return $query->where('vehicle_id', $vehicle_id)->get()->first();
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
