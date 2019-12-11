<?php

namespace App\Models\BEA;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BEA\DiscountType
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property int $default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DiscountType newModelQuery()
 * @method static Builder|DiscountType newQuery()
 * @method static Builder|DiscountType query()
 * @method static Builder|DiscountType whereCreatedAt($value)
 * @method static Builder|DiscountType whereDefault($value)
 * @method static Builder|DiscountType whereDescription($value)
 * @method static Builder|DiscountType whereIcon($value)
 * @method static Builder|DiscountType whereId($value)
 * @method static Builder|DiscountType whereName($value)
 * @method static Builder|DiscountType whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int|null $uid
 * @method static Builder|DiscountType whereUid($value)
 */
class DiscountType extends Model
{
    protected $table = 'bea_discount_types';

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }
}
