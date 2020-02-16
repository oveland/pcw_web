<?php

namespace App\Models\Users;

use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Users\UserVehicle
 *
 * @property int $id
 * @property int $user_id
 * @property int $vehicle_id
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Vehicle $vehicle
 * @method static Builder|UserVehicle whereActive($value)
 * @method static Builder|UserVehicle whereCreatedAt($value)
 * @method static Builder|UserVehicle whereId($value)
 * @method static Builder|UserVehicle whereUpdatedAt($value)
 * @method static Builder|UserVehicle whereUserId($value)
 * @method static Builder|UserVehicle whereVehicleId($value)
 * @mixin Eloquent
 */
class UserVehicle extends Model
{
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
