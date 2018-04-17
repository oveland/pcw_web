<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SimGPS
 *
 * @property int $id
 * @property string $sim
 * @property string $operator
 * @property string $gps_type
 * @property int $vehicle_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS findByVehicleId($vehicle_id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS whereGpsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS whereSim($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SimGPS whereVehicleId($value)
 * @mixin \Eloquent
 */
class SimGPS extends Model
{
    protected $table = 'sim_gps';

    protected $fillable = ['sim','operator','gps_type','vehicle_id','active'];

    public function scopeFindByVehicleId($query, $vehicle_id)
    {
        return $query->where('vehicle_id', $vehicle_id)->where('active',true);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getGPSType()
    {
        return $this->gps_type == 'TR' ? 'TRACKER':'SKYPATROL';
    }

    public function getResetCommand()
    {
        return $this->gps_type == 'TR' ? "reset123456" : 'AT$RESET';
    }
}
