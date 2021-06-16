<?php

namespace App\Models\Vehicles\Binnacles;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Vehicles\Binnacles\Notification
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $binnacle_id
 * @property string $date
 * @property int $period
 * @property int $day_of_month
 * @property int $day_of_week
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Notification whereBinnacleId($value)
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereDate($column, $value)
 * @method static Builder|Notification whereDayOfMonth($value)
 * @method static Builder|Notification whereDayOfWeek($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification wherePeriod($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 * @property-read Binnacle $binnacle
 * @property-read Collection|NotificationUser[] $notificationUsers
 * @property int|null $mileage
 * @method static Builder|Notification whereMileage($value)
 */
class Notification extends Model
{
    protected $table = 'vehicle_binnacle_notifications';

    protected $fillable = ['binnacle_id', 'date', 'period', 'day_of_month', 'day_of_week', 'mileage'];

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getDateAttribute($date)
    {
        if(!$date) return null;
        return Carbon::createFromFormat(config('app.date_format'), explode(' ', $date)[0]);
    }

    /**
     * @return BelongsTo | Binnacle
     */
    public function binnacle()
    {
        return $this->belongsTo(Binnacle::class);
    }

    /**
     * @return HasMany | NotificationUser
     */
    public function notificationUsers()
    {
        return $this->hasMany(NotificationUser::class, 'notification_id', 'id');
    }
}
