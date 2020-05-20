<?php

namespace App\Models\Apps\Rocket;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use IlluminateDatabaseEloquentBuilder;
use IlluminateDatabaseEloquentModel;
use IlluminateDatabaseEloquentRelationsBelongsTo;
use IlluminateSupportCarbon;

/**
 * AppModelsAppsRocketBatteryLog
 *
 * @property int $id
 * @property int $level
 * @property bool $charging
 * @property string $date
 * @property string $date_changed
 * @property int $vehicle_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BatteryLog newModelQuery()
 * @method static Builder|BatteryLog newQuery()
 * @method static Builder|BatteryLog query()
 * @method static Builder|BatteryLog whereCharging($value)
 * @method static Builder|BatteryLog whereCreatedAt($value)
 * @method static Builder|BatteryLog whereDate($value)
 * @method static Builder|BatteryLog whereDateChanged($value)
 * @method static Builder|BatteryLog whereId($value)
 * @method static Builder|BatteryLog whereLevel($value)
 * @method static Builder|BatteryLog whereUpdatedAt($value)
 * @method static Builder|BatteryLog whereVehicleId($value)
 * @mixin Eloquent
 * @property-read Vehicle $vehicle
 * @method static Builder|CurrentBattery findByVehicle(Vehicle $vehicle)
 */
class CurrentBattery extends Model
{
    protected $table = 'app_current_battery';

    protected $fillable = ['level', 'charging', 'date', 'date_changed', 'vehicle_id'];

    protected $dates = ['date', 'date_changed'];

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @param Builder $query
     * @param Vehicle $vehicle
     * @return Builder
     */
    function scopeFindByVehicle(Builder $query, Vehicle $vehicle)
    {
        $currentBattery = $query->where('vehicle_id', $vehicle->id)->first();
        $currentBattery = $currentBattery ? $currentBattery : new CurrentBattery();
        $currentBattery->vehicle()->associate($vehicle);
        return $currentBattery;
    }
}
