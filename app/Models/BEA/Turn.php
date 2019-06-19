<?php

namespace App\Models\BEA;

use App\Models\Drivers\Driver;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BEA\Turn
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $route_id
 * @property int $driver_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Drivers\Driver $driver
 * @property-read \App\Models\Routes\Route $route
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Turn whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read mixed $a_p_i
 */
class Turn extends Model
{
    protected $table = 'bea_turns';

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return BelongsTo
     */
    function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'bea_id');
    }

    /**
     * @return BelongsTo
     */
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'bea_id');
    }

    /**
     * @return BelongsTo
     */
    function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'bea_id');
    }

    function getAPIAttribute()
    {
        return collect([
            'route' => $this->route->toArray(),
            'vehicle' => $this->vehicle->toArray(),
            'driver' => $this->driver ? $this->driver->toArray() : [],
        ]);
    }
}
