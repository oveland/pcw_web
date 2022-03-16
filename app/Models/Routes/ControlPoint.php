<?php

namespace App\Models\Routes;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\ControlPoint
 *
 * @property-read Route $route
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ControlPoint whereCreatedAt($value)
 * @method static Builder|ControlPoint whereDistanceFromDispatch($value)
 * @method static Builder|ControlPoint whereDistanceNextPoint($value)
 * @method static Builder|ControlPoint whereId($value)
 * @method static Builder|ControlPoint whereLatitude($value)
 * @method static Builder|ControlPoint whereLongitude($value)
 * @method static Builder|ControlPoint whereName($value)
 * @method static Builder|ControlPoint whereOrder($value)
 * @method static Builder|ControlPoint whereRouteId($value)
 * @method static Builder|ControlPoint whereTrajectory($value)
 * @method static Builder|ControlPoint whereType($value)
 * @method static Builder|ControlPoint whereUpdatedAt($value)
 * @property bool $reportable
 * @method static Builder|ControlPoint whereReportable($value)
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
        $this->hasMany(ControlPointTimeReport::class)->orderBy('day_type_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
