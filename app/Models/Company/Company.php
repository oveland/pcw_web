<?php

namespace App\Models\Company;

use App\Models\Drivers\Driver;
use App\Models\Proprietaries\Proprietary;
use App\Models\Routes\Dispatch;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Company\Company
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Vehicle[] $activeVehicles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\Vehicle[] $vehicles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Drivers\Drivers[] $drivers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Drivers\Drivers[] $activeDrivers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Dispatch[] $dispatches
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
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Route[] $routes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereNit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company findAllActive()
 * @property string|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereTimezone($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Route[] $activeRoutes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Proprietaries\Proprietary[] $proprietaries
 */
class Company extends Model
{
    protected function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class)->orderBy('name');
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
        return $query->where('active','=',true)->orderBy('short_name');
    }

    public function scopeFindAllActive($query){
        return $query->where('active','=',true)->orderBy('short_name', 'asc')->get();
    }

    /*
     * What companies that have seat sensor counter
     *
     * Alameda
     *
    */
    public function hasRecorderCounter()
    {
        return $this->id == 14;
    }

    public function hasDriverRegisters()
    {
        return $this->id == 14;
    }

    /*
     * What companies that have seat sensor counter
     *
     * Cootransol
     *
    */
    public function hasSeatSensorCounter()
    {
        return $this->id == 12;
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class)->orderBy('name');
    }

    public function activeDrivers()
    {
        return $this->hasMany(Driver::class)->where('active',true);
    }

    public function proprietaries()
    {
        return $this->hasMany(Proprietary::class);
    }
}
