<?php

namespace App\Models\Vehicles\Binnacles;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\Binnacles\Type
 *
 * @property int $id
 * @property string $uid
 * @property string $name
 * @property string|null $description
 * @property bool $active
 * @property string $css_class
 * @property string $icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Type whereActive($value)
 * @method static Builder|Type whereCreatedAt($value)
 * @method static Builder|Type whereCssClass($value)
 * @method static Builder|Type whereDescription($value)
 * @method static Builder|Type whereIcon($value)
 * @method static Builder|Type whereId($value)
 * @method static Builder|Type whereName($value)
 * @method static Builder|Type whereUid($value)
 * @method static Builder|Type whereUpdatedAt($value)
 * @mixin Eloquent
 * @method static Builder|Type active()
 */
class Type extends Model
{
    protected $table = 'vehicle_binnacle_types';

    protected $fillable = ['uid', 'name', 'description', 'active', 'css_class', 'icon'];

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }
}
