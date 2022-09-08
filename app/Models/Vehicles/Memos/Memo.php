<?php

namespace App\Models\Vehicles\Memos;

use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Vehicles\Memos\Memo
 *
 * @property int $id
 * @property string $date
 * @property int $vehicle_id
 * @property int $created_user_id
 * @property int|null $edited_user_id
 * @property string $observations
 * @property string $short_observations
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $createdUser
 * @property-read User|null $editedUser
 * @property-read Vehicle $vehicle
 * @method static Builder|Memo whereCreatedAt($value)
 * @method static Builder|Memo whereCreatedUserId($value)
 * @method static Builder|Memo whereDate($value)
 * @method static Builder|Memo whereEditedUserId($value)
 * @method static Builder|Memo whereId($value)
 * @method static Builder|Memo whereObservations($value)
 * @method static Builder|Memo whereUpdatedAt($value)
 * @method static Builder|Memo whereVehicleId($value)
 * @mixin Eloquent
 */
class Memo extends Model
{
    protected $table = 'vehicle_memos';

    protected $fillable = ['date', 'vehicle_id', 'observations'];

    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    function getUpdatedAtAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    function getDateAttribute($date)
    {
        if (!$date) return null;
        return Carbon::createFromFormat(config('app.date_format'), explode('.', $date)[0]);
    }

    function getShortObservationsAttribute()
    {
        return mb_strimwidth($this->observations, 0, 50, ' •••');
    }

    /**
     * @return BelongsTo | Vehicle
     */
    function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo | User
     */
    function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id', 'id');
    }

    /**
     * @return BelongsTo | User
     */
    function editedUser()
    {
        return $this->belongsTo(User::class, 'edited_user_id', 'id');
    }
}
