<?php

namespace App\Models\Routes;

use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\DispatcherVehicle
 *
 * @property int $id
 * @property string $date
 * @property int $day_type_id
 * @property int $dispatch_id
 * @property int $route_id
 * @property int $vehicle_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\Dispatch $dispatch
 * @property-read \App\Models\Routes\Route|null $route
 * @property-read \App\Models\Vehicles\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle active()
 * @property bool|null $default
 * @property bool|null $active
 * @property-read \App\Models\Routes\DispatcherVehicle $defaultDispatcherVehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\DispatcherVehicle query()
 */
class DispatcherVehicle extends Model
{
    public $timestamps = false;

    public $fillable = ['vehicle_id', 'route_id', 'dispatch_id'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->orderBy('number');
    }

    public function route()
    {
        return $this->belongsTo(Route::class)->orderBy('name');
    }

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('route_id', '<>', null);
    }

    public function defaultDispatcherVehicle()
    {
        return $this->hasOne(DispatcherVehicle::class, 'vehicle_id', 'vehicle_id')
            //->where('dispatch_id', $this->dispatch_id)
            ->where('default', true)
            ->where('active', true);
    }
}
