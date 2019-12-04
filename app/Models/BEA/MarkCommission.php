<?php

namespace App\Models\BEA;

use App\Models\Routes\Route;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BEA\Commission
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Commission newModelQuery()
 * @method static Builder|Commission newQuery()
 * @method static Builder|Commission query()
 * @method static Builder|Commission whereCreatedAt($value)
 * @method static Builder|Commission whereId($value)
 * @method static Builder|Commission whereRouteId($value)
 * @method static Builder|Commission whereType($value)
 * @method static Builder|Commission whereUpdatedAt($value)
 * @method static Builder|Commission whereValue($value)
 * @mixin Eloquent
 * @property-read Route $route
 * @property int $mark_id
 * @method static Builder|MarkCommission whereMarkId($value)
 * @property-read \App\Models\BEA\Mark $mark
 */
class MarkCommission extends Model
{
    protected $table = 'bea_mark_commissions';

    protected $fillable = ['route_id', 'type', 'value'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return BelongsTo
     */
    function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function mark()
    {
        return $this->belongsTo(Mark::class, 'mark_id', 'id');
    }
}
