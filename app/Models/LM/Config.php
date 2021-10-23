<?php

namespace App\Models\LM;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LM\Config
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LM\Config whereValue($value)
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
