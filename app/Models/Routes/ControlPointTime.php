<?php

namespace App\Models\Routes;

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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Routes\ControlPoint $controlPoint
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereTimeFromDispatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereTimeNextPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\Fringe|null $fringe
 * @property string|null $uid
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\ControlPointTime whereUid($value)
 */
class ControlPointTime extends Model
{
    protected $hidden = ['created_at','updated_at'];

    protected $fillable = ['time', 'time_next_point', 'time_from_dispatch', 'day_type_id', 'control_point_id', 'fringe_id', 'uid'];

    protected function getDateFormat()
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
