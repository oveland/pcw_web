<?php

namespace App\Models\Vehicles;

use App\Models\Company\Company;
use App\Models\Operation\FuelStation;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
class TopologiesSeats extends Model
{
    protected $table = 'topologies_seats';
    /**
     * @return BelongsTo | Company
     * @return BelongsTo | Vehicle
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

}
