<?php

namespace App\Models\BEA;

use App\Models\Routes\Route;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BEA\Trajectory
 *
 * @method static Builder|Trajectory newModelQuery()
 * @method static Builder|Trajectory newQuery()
 * @method static Builder|Trajectory query()
 * @mixin Eloquent
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $route_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Trajectory whereCreatedAt($value)
 * @method static Builder|Trajectory whereDescription($value)
 * @method static Builder|Trajectory whereId($value)
 * @method static Builder|Trajectory whereName($value)
 * @method static Builder|Trajectory whereUpdatedAt($value)
 * @property-read Route $route
 * @method static Builder|Trajectory whereRouteId($value)
 */
class Trajectory extends Model
{
    protected $table = 'bea_trajectories';

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return BelongsTo|Route|null
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
