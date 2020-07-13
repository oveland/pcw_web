<?php

namespace App\Models\Apps\Rocket;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\Apps\Rocket\BatteryLog
 *
 * @property int $id
 * @property int $level
 * @property bool $charging
 * @property string $date
 * @property string $date_changed
 * @property int $vehicle_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Battery newModelQuery()
 * @method static Builder|Battery newQuery()
 * @method static Builder|Battery query()
 * @method static Builder|Battery whereCharging($value)
 * @method static Builder|Battery whereCreatedAt($value)
 * @method static Builder|Battery whereDate($value)
 * @method static Builder|Battery whereDateChanged($value)
 * @method static Builder|Battery whereId($value)
 * @method static Builder|Battery whereLevel($value)
 * @method static Builder|Battery whereUpdatedAt($value)
 * @method static Builder|Battery whereVehicleId($value)
 * @mixin Eloquent
 * @property-read Vehicle $vehicle
 */
class Battery extends Model
{
    protected $table = 'app_battery';

    protected $fillable = ['level', 'charging', 'date', 'date_changed', 'vehicle_id'];

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function getDateAttribute($date)
    {
        if (Str::contains($date, '-')) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date);
        }

        return Carbon::createFromFormat($this->getDateFormat(), explode('.', $date)[0]);
    }

    public function getDateChangedAttribute($dateChanged)
    {
        if (Str::contains($dateChanged, '-')) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $dateChanged);
        }

        return Carbon::createFromFormat($this->getDateFormat(), explode('.', $dateChanged)[0]);
    }

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}