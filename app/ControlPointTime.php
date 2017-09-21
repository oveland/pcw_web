<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ControlPointTime
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
 * @property-read \App\ControlPoint $controlPoint
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereControlPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereFringeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereTimeFromDispatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereTimeNextPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ControlPointTime whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ControlPointTime extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function controlPoint()
    {
        return $this->belongsTo(ControlPoint::class);
    }
}
