<?php

namespace App\Models\Vehicles\Binnacles;

use App\Models\Users\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Vehicles\Binnacles\NotificationUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $notification_id
 * @property bool $email_notified
 * @property string $email_notified_at
 * @property bool $platform_notified
 * @property string $platform_notified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Notification $notification
 * @property-read User $user
 * @method static Builder|NotificationUser whereCreatedAt($value)
 * @method static Builder|NotificationUser whereEmailNotified($value)
 * @method static Builder|NotificationUser whereEmailNotifiedAt($value)
 * @method static Builder|NotificationUser whereId($value)
 * @method static Builder|NotificationUser whereNotificationId($value)
 * @method static Builder|NotificationUser wherePlatformNotified($value)
 * @method static Builder|NotificationUser wherePlatformNotifiedAt($value)
 * @method static Builder|NotificationUser whereUpdatedAt($value)
 * @method static Builder|NotificationUser whereUserId($value)
 * @mixin Eloquent
 */
class NotificationUser extends Model
{
    protected $table = 'vehicle_binnacle_notifications_users';

    protected $fillable = ['user_id', 'notification_id', 'email_notified', 'email_notified_at', 'platform_notified', 'platform_notified_at'];

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getEmailNotifiedAtAttribute($date)
    {
        return $date ? Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]) : '';
    }

    public function getPlatformNotifiedAtAttribute($date)
    {
        return $date ? Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]) : '';
    }

    /**
     * @return BelongsTo | User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'id');
    }
}
