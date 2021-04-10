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
 * @method static Builder|ActivityLog whereDateOrRange($dateStart, $dateEnd = null)
 * @method static Builder|ActivityLog whereDateRangeAndUser($dateStart, $dateEnd = null, $user = null)
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

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    /**
     * @param Builder | ActivityLog $query
     * @param $dateStart
     * @param null $dateEnd
     * @param null $user
     * @return ActivityLog|Builder
     */
    public function scopeWhereDateRangeAndUser($query, $dateStart, $dateEnd = null, $user = null)
    {
        return $query->whereDateOrRange($dateStart, $dateEnd)->whereUserId($user);
    }

    /**
     * @param Builder | ActivityLog $query
     * @param string $dateStart
     * @param string | null $dateEnd
     * @return ActivityLog | Builder
     */
    public function scopeWhereDateOrRange($query, string $dateStart, $dateEnd = null)
    {
        if ($dateEnd) {
            $query = $query->whereBetween('created_at', [explode(' ', $dateStart)[0], explode(' ', $dateEnd)[0].' 23:59:59']);
        } else {
            $query = $query->whereDate('created_at', explode(' ', $dateStart)[0]);
        }

        return $query;
    }

    /**
     * @param Builder | ActivityLog $query
     * @param null $userId
     * @return ActivityLog | Builder
     */
    public function scopeWhereUserId($query, $userId = null)
    {
        if ($userId && $userId != 'all') {
            $query = $query->where('user_id', $userId);
        }
        return $query;
    }
}
