<?php

namespace App\Models\Routes;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Routes\RouteDispatch
 *
 * @property int $id
 * @property int $route_id
 * @property int $origin_dispatch_id
 * @property int $destination_dispatch_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|RouteDispatch whereCreatedAt($value)
 * @method static Builder|RouteDispatch whereDestinationDispatchId($value)
 * @method static Builder|RouteDispatch whereId($value)
 * @method static Builder|RouteDispatch whereOriginDispatchId($value)
 * @method static Builder|RouteDispatch whereRouteId($value)
 * @method static Builder|RouteDispatch whereUpdatedAt($value)
 * @mixin Eloquent
 */
class RouteDispatch extends Model
{
    public function route()
    {
        $this->belongsTo(Route::class);
    }

    public function dispatchOrigin()
    {
        $this->belongsTo(Dispatch::class, 'origin_dispatch_id', 'id');
    }

    public function dispatchDestination()
    {
        $this->belongsTo(Dispatch::class, 'destination_dispatch_id', 'id');
    }
}
