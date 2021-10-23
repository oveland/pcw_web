<?php

namespace App\Models\LM;

use App\Models\Company\Company;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\LM\GlobalCosts
 *
 * @property int $id
 * @property int $uid
 * @property string $name
 * @property string|null $description
 * @property int $value
 * @property string|null $concept
 * @property int|null $priority
 * @property int $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|GlobalCosts newModelQuery()
 * @method static Builder|GlobalCosts newQuery()
 * @method static Builder|GlobalCosts query()
 * @method static Builder|GlobalCosts whereCompanyId($value)
 * @method static Builder|GlobalCosts whereConcept($value)
 * @method static Builder|GlobalCosts whereCreatedAt($value)
 * @method static Builder|GlobalCosts whereDescription($value)
 * @method static Builder|GlobalCosts whereId($value)
 * @method static Builder|GlobalCosts whereName($value)
 * @method static Builder|GlobalCosts wherePriority($value)
 * @method static Builder|GlobalCosts whereUid($value)
 * @method static Builder|GlobalCosts whereUpdatedAt($value)
 * @method static Builder|GlobalCosts whereValue($value)
 * @mixin Eloquent
 * @property-read Company $company
 * @method static Builder|GlobalCosts whereCompany($query)
 */
class GlobalCosts extends Model
{
    protected $table = 'bea_global_costs';

    /**
     * @return BelongsTo | Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @param $query
     * @param Company $company
     * @return mixed
     */
    public function scopeWhereCompany($query, Company $company)
    {
        return $query->where('company_id', $company->id);
    }
}
