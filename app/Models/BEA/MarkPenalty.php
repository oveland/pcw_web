<?php

namespace App\Models\BEA;

use App\Models\Routes\Route;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BEA\Penalty
 *
 * @property int $id
 * @property int $route_id
 * @property string $type
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Penalty newModelQuery()
 * @method static Builder|Penalty newQuery()
 * @method static Builder|Penalty query()
 * @method static Builder|Penalty whereCreatedAt($value)
 * @method static Builder|Penalty whereId($value)
 * @method static Builder|Penalty whereRouteId($value)
 * @method static Builder|Penalty whereType($value)
 * @method static Builder|Penalty whereUpdatedAt($value)
 * @method static Builder|Penalty whereValue($value)
 * @mixin Eloquent
 * @property-read Route $route
 * @property-read \App\Models\BEA\Mark $mark
 */
class MarkPenalty extends Model
{
    protected $table = 'bea_mark_penalties';

    protected $fillable = ['route_id', 'type', 'value', 'created_at', 'updated_at'];

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
