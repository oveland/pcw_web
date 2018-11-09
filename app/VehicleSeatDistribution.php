<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VehicleSeatDistribution
 *
 * @property int $id
 * @property int $vehicle_id
 * @property string $json_distribution
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleSeatDistribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleSeatDistribution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleSeatDistribution whereJsonDistribution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleSeatDistribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\VehicleSeatDistribution whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehicleSeatDistribution extends Model
{
    //
}
