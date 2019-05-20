<?php

namespace App\Models\BEA;

use App\Models\Routes\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BEA\Commission
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Commission whereValue($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Routes\Route $route
 */
class Commission extends Model
{
    protected $table = 'bea_commissions';

    /**
     * @return BelongsTo
     */
    function route()
    {
        return $this->belongsTo(Route::class);
    }
}
