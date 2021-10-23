<?php

namespace App\Models\LM;

use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\LM\Penalty
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Penalty newModelQuery()
 * @method static Builder|Penalty newQuery()
 * @method static Builder|Penalty query()
 * @method static Builder|Penalty whereCreatedAt($value)
 * @method static Builder|Penalty whereId($value)
 * @method static Builder|Penalty whereRouteId($value)
 * @method static Builder|Penalty whereType($value)
 * @method static Builder|Penalty whereUpdatedAt($value)
 * @method static Builder|Penalty whereValue($value)
 * @mixin Eloquent
 * @property-read Route $route
 * @property int|null $vehicle_id
 * @property-read Vehicle|null $vehicle
 * @method static Builder|Penalty whereVehicleId($value)
 */
class Penalty extends Model
{
    protected $table = 'bea_penalties';

    protected $fillable = ['route_id', 'type', 'vehicle_id', 'value'];

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

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
