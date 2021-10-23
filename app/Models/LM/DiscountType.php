<?php

namespace App\Models\LM;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\LM\DiscountType
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
 * @property int $company_id
 * @method static Builder|DiscountType whereCompanyId($value)
 * @property bool $required
 * @method static Builder|DiscountType whereRequired($value)
 * @property bool $optional
 * @method static Builder|DiscountType whereOptional($value)
 */
class DiscountType extends Model
{
    protected $table = 'bea_discount_types';

    protected $fillable = ['uid', 'name', 'icon', 'description', 'default', 'company_id', 'required', 'optional'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getAPIFields()
    {
        return (object)[
            'id' => $this->id,
            'uid' => $this->uid,
            'name' => $this->name,
            'icon' => $this->icon,
            'description' => $this->description,
            'default' => $this->default,
            'required' => $this->required,
            'optional' => $this->optional,
        ];
    }
}
