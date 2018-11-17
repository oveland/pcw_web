<?php

namespace App\Models\Routes;

use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereArrivalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereDepartureTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereRoundTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereRouteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereVehicleId($value)
 * @mixin \Eloquent
 * @property string|null $date
 * @property string|null $driver_code
 * @property-read \App\Models\Vehicles\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\CurrentDispatchRegister whereDriverCode($value)
 */
class CurrentDispatchRegister extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
