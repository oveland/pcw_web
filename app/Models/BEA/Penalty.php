<?php

namespace App\Models\BEA;

use App\Models\Routes\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BEA\Penalty
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Penalty whereValue($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\Route $route
 */
class Penalty extends Model
{
    protected $table = 'bea_penalties';

    /**
     * @return BelongsTo
     */
    function route()
    {
        return $this->belongsTo(Route::class);
    }
}
