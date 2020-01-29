<?php

namespace App\Models\Reports;

use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
