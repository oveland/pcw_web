<?php

namespace App\Models\BEA;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BEA\DiscountType
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\DiscountType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DiscountType extends Model
{
    protected $table = 'bea_discount_types';

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }
}
