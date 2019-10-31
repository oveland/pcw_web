<?php

namespace App\Models\Routes;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Routes\CurrentDispatchRegister
 *
 * @property int|null $dispatch_register_id
 * @property int|null $vehicle_id
 * @property string|null $plate
 * @property int|null $route_id
 * @property string|null $route_name
 * @property string|null $round_trip
 * @property string|null $departure_time
 * @property string|null $arrival_time
 * @method static Builder|CurrentDispatchRegister whereArrivalTime($value)
 * @method static Builder|CurrentDispatchRegister whereDepartureTime($value)
 * @method static Builder|CurrentDispatchRegister whereDispatchRegisterId($value)
 * @method static Builder|CurrentDispatchRegister wherePlate($value)
 * @method static Builder|CurrentDispatchRegister whereRoundTrip($value)
 * @method static Builder|CurrentDispatchRegister whereRouteId($value)
 * @method static Builder|CurrentDispatchRegister whereRouteName($value)
 * @method static Builder|CurrentDispatchRegister whereVehicleId($value)
 * @mixin Eloquent
 * @property string|null $date
 * @property string|null $driver_code
 * @property-read Vehicle|null $vehicle
 * @method static Builder|CurrentDispatchRegister whereDate($value)
 * @method static Builder|CurrentDispatchRegister whereDriverCode($value)
 * @property-read DispatchRegister $dispatchRegister
 */
class CurrentDispatchRegister extends Model
{
    /**
     * @return BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dispatchRegister(){
        return $this->hasOne(DispatchRegister::class);
    }
}
