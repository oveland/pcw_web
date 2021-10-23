<?php

namespace App\Models\LM;

use App\Models\Users\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\LM\Taking
 *
 * @method static Builder|Taking newModelQuery()
 * @method static Builder|Taking newQuery()
 * @method static Builder|Taking query()
 * @mixin Eloquent
 * @property-read Liquidation $liquidation
 * @property-read User $user
 * @property int $id
 * @property string $date
 * @property int|null $liquidation_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Taking whereCreatedAt($value)
 * @method static Builder|Taking whereDate($value)
 * @method static Builder|Taking whereId($value)
 * @method static Builder|Taking whereLiquidationId($value)
 * @method static Builder|Taking whereUpdatedAt($value)
 * @method static Builder|Taking whereUserId($value)
 */
class Taking extends Model
{
    protected $table = 'bea_takings';

    /**
     * @return Liquidation
     */
    function liquidation(){
        return $this->belongsTo(Liquidation::class);
    }

    /**
     * @return User
     */
    function user()
    {
        return $this->belongsTo(User::class);
    }
}
