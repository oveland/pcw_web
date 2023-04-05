<?php

namespace App\Models\Apps\Rocket;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\Apps\Rocket\ConfigProfile
 *
 * @property int $id
 * @property int $vehicle_id
 * @property string $camera
 * @property Carbon|null $date
 * @property string $config
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ConfigProfile newModelQuery()
 * @method static Builder|ConfigProfile newQuery()
 * @method static Builder|ConfigProfile query()
 * @method static Builder|ConfigProfile whereConfig($value)
 * @method static Builder|ConfigProfile whereCreatedAt($value)
 * @method static Builder|ConfigProfile whereId($value)
 * @method static Builder|ConfigProfile whereUpdatedAt($value)
 * @method static Builder|ConfigProfile whereVehicleId($value)
 * @mixin Eloquent
 * @property-read Vehicle $vehicle
 */
class ConfigProfile extends Model
{
    protected $table = 'app_config_profiles';

    public function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @param $date
     * @return string
     */
    public function getDateAttribute($date)
    {
        if (Str::contains($date, '-')) {
            return Carbon::createFromFormat('Y-m-d', explode(' ', $date)[0])->toDateString();
        }

        return Carbon::createFromFormat(config('app.date_format'), explode(' ', $date)[0])->toDateString();
    }


    /**
     * @return BelongsTo | Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getConfigAttribute($config)
    {
        return json_decode($config, true);
    }

    public function setConfigAttribute($config)
    {
        $this->attributes['config'] = json_encode($config);
    }

    /**
     * @param $type
     * @return object
     */
    function type($type)
    {
        $allConfig = collect($this->config);
        $typeConfig = $allConfig->get($type);

        $allConfig->filter(function($data) {
           return isset($data['photo']);
        })->keys()->each(function($k) use (&$allConfig) {
            $allConfig->forget($k);
        });

        return (object)$allConfig->merge($typeConfig);
    }
}
