<?php

namespace App\Models\Apps\Rocket;

use App\Models\Vehicles\Vehicle;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\Apps\Rocket\ProfileSeat
 *
 * @property int $id
 * @property int $vehicle_id
 * @property object $occupation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Vehicle $vehicle
 * @property string $photo
 * @method static Builder|ProfileSeat newModelQuery()
 * @method static Builder|ProfileSeat newQuery()
 * @method static Builder|ProfileSeat query()
 * @method static Builder|ProfileSeat whereCreatedAt($value)
 * @method static Builder|ProfileSeat whereId($value)
 * @method static Builder|ProfileSeat whereOccupation($value)
 * @method static Builder|ProfileSeat whereUpdatedAt($value)
 * @method static Builder|ProfileSeat whereVehicleId($value)
 * @method static Builder|ProfileSeat wherePhoto($value)
 * @mixin Eloquent
 * @method static Builder|ProfileSeat findByVehicle(Vehicle $vehicle)
 */
class ProfileSeat extends Model
{
    protected $table = 'app_profile_seats';

    protected $fillable = ['occupation'];

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

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function setOccupationAttribute($data)
    {
        $this->attributes['occupation'] = collect($data)->toJson();
    }

    /**
     * @param $occupation
     * @return object
     */
    function getOccupationAttribute($occupation)
    {
        return collect($occupation && Str::of($occupation)->startsWith('[') && Str::of($occupation)->endsWith(']') ? (object)json_decode($occupation, true) : null)->values();
    }

    /**
     * @param Builder $query
     * @param Vehicle $vehicle
     * @return Builder
     */
    function scopeFindByVehicle(Builder $query, Vehicle $vehicle)
    {
        $profileSeat = $query->where('vehicle_id', $vehicle->id)->first();
        $profileSeat = $profileSeat ? $profileSeat : new ProfileSeat();
        $profileSeat->vehicle()->associate($vehicle);
        $profileSeat->save();
        return $profileSeat;
    }
}
