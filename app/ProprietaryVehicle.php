<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProprietaryVehicle
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $proprietary_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Proprietary $proprietary
 * @property-read \App\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProprietaryVehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProprietaryVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProprietaryVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProprietaryVehicle whereProprietaryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProprietaryVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProprietaryVehicle whereVehicleId($value)
 * @mixin \Eloquent
 */
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
