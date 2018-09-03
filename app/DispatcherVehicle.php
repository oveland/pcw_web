<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\DispatcherVehicle
 *
 * @property int $id
 * @property string $date
 * @property int $day_type_id
 * @property int $dispatch_id
 * @property int $route_id
 * @property int $vehicle_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatcherVehicle whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatcherVehicle whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatcherVehicle whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatcherVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatcherVehicle whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DispatcherVehicle whereVehicleId($value)
 * @mixin \Eloquent
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
}
