<?php

namespace App\Models\Operation;

use App\Models\Company\Company;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * App\Models\Operation\FuelStation
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FuelStation whereCreatedAt($value)
 * @method static Builder|FuelStation forCompany($company)
 * @method static Builder|FuelStation whereDescription($value)
 * @method static Builder|FuelStation whereId($value)
 * @method static Builder|FuelStation whereName($value)
 * @method static Builder|FuelStation whereUpdatedAt($value)
 * @mixin Eloquent
 */
class FuelStation extends Model
{
    /**
     * @return BelongsTo | Company
     */
    function company()
    {
        return $this->belongsTo(Company::class);
    }

    function scopeForCompany(Builder $query, Company $company)
    {
        return $query->where('company_id', $company->id);
    }

    /**
     * @param Company $company
     * @return FuelStation[]|Builder[]|Collection
     */
    static function allByCompany(Company $company)
    {
        $fuelStations = FuelStation::forCompany($company)->get();

        if (!$fuelStations->count()) {
            $fs = new FuelStation();
            $fs->company()->associate($company);
            $fs->name = 'EDS X';
            $fs->description = 'EDS X';
            $fs->save();

            $fuelStations = [$fs];
        }

        return $fuelStations;
    }

    public function toArray()
    {
        return (object)[
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
