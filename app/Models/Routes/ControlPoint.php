<?php

namespace App\Models\Routes;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\ControlPoint
 *
 * @property-read \App\Models\Routes\Route $route
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereDistanceFromDispatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereDistanceNextPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereTrajectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereUpdatedAt($value)
 * @property bool $reportable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPoint whereReportable($value)
 */
class ControlPoint extends Model
{
    const INITIAL = 'Inicial';
    const FINAL = 'Final';
    const RETURN = 'Final';

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function times()
    {
        $this->hasMany(ControlPointTimeReport::class)->orderBy('day_type_id','asc');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
