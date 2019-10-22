<?php

namespace App\Models\Routes;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\Fringe
 *
 * @property int $id
 * @property string $name
 * @property string $from
 * @property string $to
 * @property bool $active
 * @property int $route_id
 * @property int $day_type_id
 * @property string $style_color
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Routes\DayType $dayType
 * @property-read \App\Models\Routes\Route $route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $sequence
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereSequence($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\ControlPointTime[] $controlPointTimes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereStyleColor($value)
 * @property string|null $uid
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereUid($value)
 * @property string|null $time_from
 * @property string|null $time_to
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereTimeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Fringe whereTimeTo($value)
 */
class Fringe extends Model
{
    protected $hidden = ['created_at','updated_at'];

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function dayType()
    {
        return $this->belongsTo(DayType::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function controlPointTimes()
    {
        return $this->hasMany(ControlPointTime::class);
    }
}
