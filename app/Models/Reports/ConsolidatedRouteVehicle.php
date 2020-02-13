<?php

namespace App\Models\Reports;

use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Reports\ConsolidatedRouteVehicle
 *
 * @property int $id
 * @property string $date
 * @property int $route_id
 * @property int $vehicle_id
 * @property int $total_off_roads
 * @property int $total_speeding
 * @property int $total_locations
 * @property string|null $observations
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Routes\Route $route
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereTotalLocations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereTotalOffRoads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereTotalSpeeding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Reports\ConsolidatedRouteVehicle whereVehicleId($value)
 * @mixin \Eloquent
 */
class ConsolidatedRouteVehicle extends Model
{
    protected $fillable = ['date', 'route_id', 'vehicle_id', 'total_off_roads', 'total_speeding', 'total_locations'];

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.date_format'), $date);
    }

    /**
     * @return Route | BelongsTo
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return Vehicle | BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
