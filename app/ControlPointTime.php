<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/*
 * App\ControlPointTime
 */

/**
 * App\ControlPointTime
 *
 * @property mixed $control_point
 * @property int $id
 * @property string $time
 * @property string $time_from_dispatch
 * @property int $day_type_id
 * @property int $control_point_id
 * @property int $fringe_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\ControlPoint $controlPoint
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereTimeFromDispatch($value)
 * @mixin \Eloquent
 */
class ControlPointTime extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    public function controlPoint()
    {
        return $this->belongsTo(ControlPoint::class);
    }
}
