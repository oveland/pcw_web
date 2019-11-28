<?php

namespace App\Models\Routes;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\ControlPointTime
 *
 * @property int $id
 * @property string $time
 * @property string $time_from_dispatch
 * @property string $time_next_point
 * @property int $day_type_id
 * @property int $control_point_id
 * @property int|null $fringe_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ControlPoint $controlPoint
 * @method static Builder|ControlPointTime whereControlPointId($value)
 * @method static Builder|ControlPointTime whereCreatedAt($value)
 * @method static Builder|ControlPointTime whereDayTypeId($value)
 * @method static Builder|ControlPointTime whereFringeId($value)
 * @method static Builder|ControlPointTime whereId($value)
 * @method static Builder|ControlPointTime whereTime($value)
 * @method static Builder|ControlPointTime whereTimeFromDispatch($value)
 * @method static Builder|ControlPointTime whereTimeNextPoint($value)
 * @method static Builder|ControlPointTime whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Fringe|null $fringe
 * @method static Builder|ControlPointTime newModelQuery()
 * @method static Builder|ControlPointTime newQuery()
 * @method static Builder|ControlPointTime query()
 * @property string|null $uid
 * @method static Builder|ControlPointTime whereUid($value)
 */
class ControlPointTime extends Model
{
    protected $hidden = ['created_at','updated_at'];

    protected $fillable = ['time', 'time_next_point', 'time_from_dispatch', 'day_type_id', 'control_point_id', 'fringe_id', 'uid'];

    public function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function controlPoint()
    {
        return $this->belongsTo(ControlPoint::class);
    }

    public function fringe()
    {
        return $this->belongsTo(Fringe::class);
    }
}
