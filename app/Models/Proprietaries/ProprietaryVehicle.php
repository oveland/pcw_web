<?php

namespace App\Models\Proprietaries;

use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Proprietaries\ProprietaryVehicle
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $proprietary_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Proprietaries\Proprietary $proprietary
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereProprietaryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\ProprietaryVehicle whereVehicleId($value)
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
        return $this->belongsTo(ProprietaryVehicle::class);
    }
}
