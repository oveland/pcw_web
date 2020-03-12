<?php

namespace App\Models\BEA;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BEA\Config
 *
 * @property int $id
 * @property int $uid
 * @property int $company_id
 * @property string $key
 * @property string $value
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BEA\Config whereValue($value)
 * @mixin \Eloquent
 */
class Config extends Model
{
    protected $table = 'bea_config';

    /**
     * @return BelongsTo | Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
