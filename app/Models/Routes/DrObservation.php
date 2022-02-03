<?php

namespace App\Models\Routes;

use App\Models\Users\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Routes\DrObservation
 *
 * @property int $id
 * @property string $field
 * @property string|null $value
 * @property string|null $old_value
 * @property string $observation
 * @property int $dispatch_register_id
 * @property DispatchRegister $dispatchRegister
 * @property int $user_id
 * @property User $user
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DrObservation whereCreatedAt($value)
 * @method static Builder|DrObservation whereDispatchRegisterId($value)
 * @method static Builder|DrObservation whereField($value)
 * @method static Builder|DrObservation whereId($value)
 * @method static Builder|DrObservation whereObservation($value)
 * @method static Builder|DrObservation whereUpdatedAt($value)
 * @method static Builder|DrObservation whereValue($value)
 * @mixin Eloquent
 */
class DrObservation extends Model
{
    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    function dispatchRegister(): BelongsTo
    {
        return $this->belongsTo(DispatchRegister::class);
    }

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
