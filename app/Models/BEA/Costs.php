<?php

namespace App\Models\BEA;

use App\Models\Company\Company;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\BEA\Costs
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
 * @method static Builder|Costs newModelQuery()
 * @method static Builder|Costs newQuery()
 * @method static Builder|Costs query()
 * @method static Builder|Costs whereCompanyId($value)
 * @method static Builder|Costs whereConcept($value)
 * @method static Builder|Costs whereCreatedAt($value)
 * @method static Builder|Costs whereDescription($value)
 * @method static Builder|Costs whereId($value)
 * @method static Builder|Costs whereName($value)
 * @method static Builder|Costs wherePriority($value)
 * @method static Builder|Costs whereUid($value)
 * @method static Builder|Costs whereUpdatedAt($value)
 * @method static Builder|Costs whereValue($value)
 * @mixin Eloquent
 * @property-read Company $company
 * @method static Builder|Costs whereCompany($query)
 */
class Costs extends Model
{
    protected $table = 'bea_costs';

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
