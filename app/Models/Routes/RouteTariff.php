<?php

namespace App\Models\Routes;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Routes\Tariff
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $route_id
 * @property int $value
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|RouteTariff whereActive($value)
 * @method static Builder|RouteTariff whereCreatedAt($value)
 * @method static Builder|RouteTariff whereId($value)
 * @method static Builder|RouteTariff whereRouteId($value)
 * @method static Builder|RouteTariff whereUpdatedAt($value)
 * @method static Builder|RouteTariff whereValue($value)
 * @property-read Route $route
 * @method static Builder|RouteTariff newModelQuery()
 * @method static Builder|RouteTariff newQuery()
 * @method static Builder|RouteTariff query()
 * @property int $passenger
 * @property int $fuel
 * @method static Builder|RouteTariff whereFuel($value)
 * @method static Builder|RouteTariff wherePassenger($value)
 */
class RouteTariff extends Model
{
    /**
     * @return BelongsTo | Route
     */
    function route()
    {
        return $this->belongsTo(Route::class);
    }
}
