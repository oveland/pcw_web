<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Company
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Vehicle[] $activeVehicles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Vehicle[] $vehicles
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $nit
 * @property string|null $address
 * @property string $link
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property mixed $routes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereNit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company active()
 * @property string|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Company whereTimezone($value)
 */
class Company extends Model
{
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function activeVehicles()
    {
        return $this->hasMany(Vehicle::class)->where('active',true);
    }

    public function activeRoutes()
    {
        return $this->hasMany(Route::class)->where('active',true);
    }

    public function scopeActive($query){
        return $query->where('active','=',true);
    }

    public function hasRecorderCounter()
    {
        return $this->id == 14;
    }
}
