<?php

namespace App\Models\Routes;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DayType $dayType
 * @property-read Route $route
 * @method static Builder|Fringe whereActive($value)
 * @method static Builder|Fringe whereCreatedAt($value)
 * @method static Builder|Fringe whereDayTypeId($value)
 * @method static Builder|Fringe whereFrom($value)
 * @method static Builder|Fringe whereId($value)
 * @method static Builder|Fringe whereName($value)
 * @method static Builder|Fringe whereRouteId($value)
 * @method static Builder|Fringe whereTo($value)
 * @method static Builder|Fringe whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int $sequence
 * @method static Builder|Fringe whereSequence($value)
 * @property-read Collection|ControlPointTime[] $controlPointTimes
 * @method static Builder|Fringe whereStyleColor($value)
 * @property string|null $uid
 * @method static Builder|Fringe whereUid($value)
 * @property string|null $time_from
 * @property string|null $time_to
 * @method static Builder|Fringe whereTimeFrom($value)
 * @method static Builder|Fringe whereTimeTo($value)
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
