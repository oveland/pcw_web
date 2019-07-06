<?php

namespace App\Models\Vehicles;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\VehicleSeatTopology
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|VehicleSeatTopology whereCreatedAt($value)
 * @method static Builder|VehicleSeatTopology whereDescription($value)
 * @method static Builder|VehicleSeatTopology whereId($value)
 * @method static Builder|VehicleSeatTopology whereName($value)
 * @method static Builder|VehicleSeatTopology whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class VehicleSeatTopology extends Model
{
    public const GENERIC = 1;
    public const GUALAS = 2;
    public const INTERMUNICIPAL = 3;
}
