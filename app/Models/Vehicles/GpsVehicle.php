<?php

namespace App\Models\Vehicles;

use App\Models\Users\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicles\GpsVehicle
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $vehicle_id
 * @property string $imei
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|GpsVehicle findBySim($sim)
 * @method static Builder|GpsVehicle whereCreatedAt($value)
 * @method static Builder|GpsVehicle whereId($value)
 * @method static Builder|GpsVehicle whereImei($value)
 * @method static Builder|GpsVehicle whereUpdatedAt($value)
 * @method static Builder|GpsVehicle whereVehicleId($value)
 * @property-read Vehicle $vehicle
 * @property int|null $gps_type_id
 * @property int|null $report_period
 * @method static Builder|GpsVehicle whereGpsTypeId($value)
 * @method static Builder|GpsVehicle whereReportPeriod($value)
 * @method static Builder|GpsVehicle newModelQuery()
 * @method static Builder|GpsVehicle newQuery()
 * @method static Builder|GpsVehicle query()
 * @property-read GPSType|null $type
 * @property string|null $tags
 * @method static Builder|GpsVehicle findByVehicleId($vehicle_id)
 * @method static Builder|GpsVehicle whereTags($value)
 */
class GpsVehicle extends Model
{
    protected $fillable = ['imei'];

    protected static function boot()
    {
        parent::boot();
        static::saving(function (GpsVehicle $gps) {
            $user = \Auth::user();
            if ($user) $gps->user()->associate($user);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function hasValidImei()
    {
        return strlen($this->imei) == 15;
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function type()
    {
        return $this->belongsTo(GPSType::class, 'gps_type_id', 'id');
    }

    public function scopeFindByVehicleId($query, $vehicle_id)
    {
        return $query->where('vehicle_id', $vehicle_id)->get()->first();
    }
}
