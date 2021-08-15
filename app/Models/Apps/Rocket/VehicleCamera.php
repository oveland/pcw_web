<?php

namespace App\Models\Apps\Rocket;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Apps\Rocket\VehicleCamera
 *
 * @property int $id
 * @property string $camera
 * @property-read Vehicle $vehicle
 * @property int $vehicle_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\VehicleCamera newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\VehicleCamera newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\VehicleCamera query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\VehicleCamera whereCamera($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\VehicleCamera whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\VehicleCamera whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\VehicleCamera whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Apps\Rocket\VehicleCamera whereVehicleId($value)
 * @mixin Eloquent
 */
class VehicleCamera extends Model
{
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
