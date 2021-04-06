<?php

namespace App\Models\Reports\Activity;

use App\Models\Users\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Reports\ActivityLog
 *
 * @property int $id
 * @property string|null $route_name
 * @property string $category1
 * @property string|null $category2
 * @property string|null $category3
 * @property string $url
 * @property string|null $params
 * @property string|null $method
 * @property string|null $agent
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ActivityLog whereAgent($value)
 * @method static Builder|ActivityLog whereCategory1($value)
 * @method static Builder|ActivityLog whereCategory2($value)
 * @method static Builder|ActivityLog whereCategory3($value)
 * @method static Builder|ActivityLog whereCreatedAt($value)
 * @method static Builder|ActivityLog whereId($value)
 * @method static Builder|ActivityLog whereMethod($value)
 * @method static Builder|ActivityLog whereParams($value)
 * @method static Builder|ActivityLog whereUpdatedAt($value)
 * @method static Builder|ActivityLog whereUserId($value)
 * @mixin Eloquent
 * @property-read User|null $user
 * @method static Builder|ActivityLog whereRouteName($value)
 * @method static Builder|ActivityLog whereUrl($value)
 */
class ActivityLog extends Model
{
    protected $fillable = ['route_name', 'category1', 'category2', 'category3', 'url', 'params', 'method', 'agent'];

    /**
     * @return BelongsTo | User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
