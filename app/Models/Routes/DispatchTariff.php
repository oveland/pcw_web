<?php

namespace App\Models\Routes;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Routes\DispatchTariff
 *
 * @property int $id
 * @property int $dispatch_register_id
 * @property int $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DispatchTariff whereCreatedAt($value)
 * @method static Builder|DispatchTariff whereDispatchRegisterId($value)
 * @method static Builder|DispatchTariff whereId($value)
 * @method static Builder|DispatchTariff whereUpdatedAt($value)
 * @method static Builder|DispatchTariff whereValue($value)
 * @mixin Eloquent
 * @property-read \App\Models\Routes\DispatchRegister $dispatchRegister
 */
class DispatchTariff extends Model
{
    /**
     * @return BelongsTo | DispatchRegister
     */
    function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class);
    }
}
