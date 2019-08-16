<?php

namespace App\Models\BEA;

use App\Models\Routes\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BEA\Trajectory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $route_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereUpdatedAt($value)
 * @property-read \App\Models\Routes\Route $route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Trajectory whereRouteId($value)
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
