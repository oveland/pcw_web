<?php

namespace App\Models\Vehicles\Binnacles;

use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Vehicles\Binnacles\Binnacle
 *
 * @property int $id
 * @property string $date
 * @property int $type_id
 * @property int $vehicle_id
 * @property int $user_id
 * @property string|null $observations
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Binnacle whereCreatedAt($value)
 * @method static Builder|Binnacle whereDate($value)
 * @method static Builder|Binnacle whereId($value)
 * @method static Builder|Binnacle whereObservations($value)
 * @method static Builder|Binnacle whereTypeId($value)
 * @method static Builder|Binnacle whereUpdatedAt($value)
 * @method static Builder|Binnacle whereUserId($value)
 * @method static Builder|Binnacle whereVehicleId($value)
 * @mixin Eloquent
 * @property-read Type $type
 * @property-read User $user
 * @property-read Vehicle $vehicle
 * @property-read \App\Models\Vehicles\Binnacles\Notification $notification
 */
class Binnacle extends Model
{
    protected $table = 'vehicle_binnacles';

    protected $fillable = ['date', 'type_id', 'vehicle_id', 'user_id', 'observations'];

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    /**
     * @return BelongsTo | Type
     */
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
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
        return $this->hasOne(Notification::class, 'binnacle_id', 'id');
    }
}
