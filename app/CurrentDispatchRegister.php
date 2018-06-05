<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CurrentDispatchRegister
 *
 * @property int|null $dispatch_register_id
 * @property int|null $vehicle_id
 * @property string|null $plate
 * @property int|null $route_id
 * @property string|null $route_name
 * @property string|null $round_trip
 * @property string|null $departure_time
 * @property string|null $arrival_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentDispatchRegister whereArrivalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentDispatchRegister whereDepartureTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentDispatchRegister whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentDispatchRegister wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentDispatchRegister whereRoundTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentDispatchRegister whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentDispatchRegister whereRouteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrentDispatchRegister whereVehicleId($value)
 * @mixin \Eloquent
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
