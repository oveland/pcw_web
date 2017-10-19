<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Fringe
 *
 * @property int $id
 * @property string $name
 * @property string $from
 * @property string $to
 * @property bool $active
 * @property int $route_id
 * @property int $day_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\DayType $dayType
 * @property-read \App\Route $route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereDayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $sequence
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fringe whereSequence($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ControlPointTime[] $controlPointTimes
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
