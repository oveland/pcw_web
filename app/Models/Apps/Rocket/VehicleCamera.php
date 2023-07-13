<?php

namespace App\Models\Apps\Rocket;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|VehicleCamera newModelQuery()
 * @method static Builder|VehicleCamera newQuery()
 * @method static Builder|VehicleCamera query()
 * @method static Builder|VehicleCamera whereCamera($value)
 * @method static Builder|VehicleCamera whereCreatedAt($value)
 * @method static Builder|VehicleCamera whereId($value)
 * @method static Builder|VehicleCamera whereUpdatedAt($value)
 * @method static Builder|VehicleCamera whereVehicleId($value)
 * @mixin Eloquent
 */
class VehicleCamera extends Model
{
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
