<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ControlPoint
 *
 * @property-read \App\Route $route
 * @mixin \Eloquent
 * @property int $id
 * @property string $latitude
 * @property string $longitude
 * @property string $name
 * @property int $order
 * @property int $trajectory
 * @property string $type
 * @property int $distance_from_dispatch
 * @property int $distance_next_point
 * @property int $route_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereDistanceFromDispatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereDistanceNextPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereTrajectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPoint whereUpdatedAt($value)
 */
class ControlPoint extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    public function times()
    {
        $this->hasMany(ControlPointTime::class)->orderBy('day_type_id','asc');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
