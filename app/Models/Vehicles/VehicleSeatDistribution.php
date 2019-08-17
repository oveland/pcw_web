<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Vehicles\VehicleSeatDistribution
 *
 * @property int $id
 * @property int $vehicle_id
 * @property string $json_distribution
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|VehicleSeatDistribution whereCreatedAt($value)
 * @method static Builder|VehicleSeatDistribution whereId($value)
 * @method static Builder|VehicleSeatDistribution whereJsonDistribution($value)
 * @method static Builder|VehicleSeatDistribution whereUpdatedAt($value)
 * @method static Builder|VehicleSeatDistribution whereVehicleId($value)
 * @property int $vehicle_seat_topology_id
 * @method static Builder|VehicleSeatDistribution whereVehicleSeatTopologyId($value)
 * @method static Builder|VehicleSeatDistribution newModelQuery()
 * @method static Builder|VehicleSeatDistribution newQuery()
 * @method static Builder|VehicleSeatDistribution query()
 * @property-read VehicleSeatTopology $seatTopology
 * @property-read VehicleSeatTopology $topology
 * @mixin Eloquent
 */
class VehicleSeatDistribution extends Model
{
    /**
     * @return VehicleSeatTopology | BelongsTo
     */
    public function topology()
    {
        return $this->belongsTo(VehicleSeatTopology::class, 'vehicle_seat_topology_id', 'id');
    }
}
