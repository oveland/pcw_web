<?php

namespace App\Models\LM;

use App\Models\Drivers\Driver;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\LM\Turn
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $route_id
 * @property int $driver_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Driver $driver
 * @property-read Route $route
 * @property-read Vehicle $vehicle
 * @method static Builder|Turn newModelQuery()
 * @method static Builder|Turn newQuery()
 * @method static Builder|Turn query()
 * @method static Builder|Turn whereCreatedAt($value)
 * @method static Builder|Turn whereDriverId($value)
 * @method static Builder|Turn whereId($value)
 * @method static Builder|Turn whereRouteId($value)
 * @method static Builder|Turn whereUpdatedAt($value)
 * @method static Builder|Turn whereVehicleId($value)
 * @mixin Eloquent
 * @property-read mixed $a_p_i
 * @property int|null $bea_id
 * @property int $company_id
 * @method static Builder|Turn whereBeaId($value)
 * @method static Builder|Turn whereCompanyId($value)
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
        return $this->belongsTo(Route::class);
    }

    /**
     * @return BelongsTo
     */
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo
     */
    function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    function getAPIFields()
    {
        return (object)[
            'route' => $this->route->toArray(),
            'vehicle' => $this->vehicle->toArray(),
            'driver' => $this->driver ? $this->driver->toArray() : [],
        ];
    }
}
