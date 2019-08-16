<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\VehicleSeatDistribution
 *
 * @property int $id
 * @property int $vehicle_id
 * @property string $json_distribution
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution whereJsonDistribution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution whereVehicleId($value)
 * @mixin \Eloquent
 * @property int $vehicle_seat_topology_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution whereVehicleSeatTopologyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\VehicleSeatDistribution query()
 */
class VehicleSeatDistribution extends Model
{
    //
}
