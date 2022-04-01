<?php

namespace App\Models\Routes;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\ControlPointsTariff
 *
 * @property int $id
 * @property int $from_control_point_id
 * @property int $to_control_point_id
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property ControlPoint $fromControlPoint
 * @property ControlPoint $toControlPoint
 * @method static Builder|ControlPointsTariff whereCreatedAt($value)
 * @method static Builder|ControlPointsTariff whereFromControlPointId($value)
 * @method static Builder|ControlPointsTariff whereId($value)
 * @method static Builder|ControlPointsTariff whereToControlPointId($value)
 * @method static Builder|ControlPointsTariff whereUpdatedAt($value)
 * @method static Builder|ControlPointsTariff whereValue($value)
 * @method static Builder|ControlPointsTariff forCompany($id)
 * @method static Builder|ControlPointsTariff forRoute($id)
 * @mixin Eloquent
 */
class ControlPointsTariff extends Model
{
    function scopeForCompany(Builder $query, $id)
    {
        $id = $id ?? 0;
        return $query->whereRaw("from_control_point_id IN (SELECT id FROM control_points WHERE route_id IN (SELECT id FROM routes WHERE company_id = $id))");
    }

    function scopeForRoute(Builder $query, $id)
    {
        $id = $id ?? 0;
        return $query->whereRaw("from_control_point_id IN (SELECT id FROM control_points WHERE route_id = $id)");
    }

    function fromControlPoint()
    {
        return $this->belongsTo(ControlPoint::class, 'from_control_point_id', 'id');
    }

    function toControlPoint()
    {
        return $this->belongsTo(ControlPoint::class, 'to_control_point_id', 'id');
    }
}
