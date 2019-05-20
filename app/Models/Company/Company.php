<?php

namespace App\Models\Company;

use App\Models\Drivers\Driver;
use App\Models\Proprietaries\Proprietary;
use App\Models\Routes\Dispatch;
use App\Models\Routes\DispatcherVehicle;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company query()
 */
class Company extends Model
{
    const PCW = 6;
    const COOTRANSOL = 12;
    const ALAMEDA = 14;
    const MONTEBELLO = 21;
    const TUPAL = 28;
    const YUMBENOS = 17;

    /**
     * @return mixed|string
     */
    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class)->orderBy('number');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeVehicles()
    {
        return $this->vehicles()->where('active', true)->orderBy('number');
    }

    /**
     * @param null $routeId
     * @return Vehicle|Vehicle[]
     */
    public function userVehicles($routeId = null)
    {
        $user = Auth::user();
        $vehicles = $user->assignedVehicles($this);
        if( $routeId && $routeId != 'all' ){
            $vehiclesIdFromDispatcherVehicles = DispatcherVehicle::where('route_id', $routeId)->get()->pluck('vehicle_id');
            $vehicles = $vehicles->whereIn('id', $vehiclesIdFromDispatcherVehicles);
        }

        return $vehicles;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function routes()
    {
        $user = Auth::user();
        $routes = $this->hasMany(Route::class)->orderBy('name');

        if ($user && $user->isProprietary()) {
            $assignedVehicles = $user->assignedVehicles(null, true);
            $routes->whereIn('id', DispatcherVehicle::whereIn('vehicle_id', $assignedVehicles->pluck('id'))->pluck('route_id'));
        }

        return $routes;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeRoutes()
    {
        return $this->routes()->where('active', true)->orderBy('name');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('active', '=', true)->orderBy('short_name');
    }

    public function scopeFindAllActive($query)
    {
        return $query->where('active', '=', true)->orderBy('short_name', 'asc')->get();
    }

    /*
     * What companies that have seat sensor counter
     *
     * Alameda
     *
    */
    public function hasRecorderCounter()
    {
        return collect([self::ALAMEDA])->contains($this->id);
    }

    /**
     * @return bool
     */
    public function hasDriverRegisters()
    {
        return collect([self::ALAMEDA])->contains($this->id);
    }

    /*
     * What companies that have seat sensor counter
     *
     * Cootransol
     *
    */
    public function hasSeatSensorCounter()
    {
        return collect([self::COOTRANSOL])->contains($this->id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class)->orderBy('first_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeDrivers()
    {
        return $this->drivers()->active();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatches()
    {
        return $this->hasMany(Dispatch::class)->orderBy('name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proprietaries()
    {
        return $this->hasMany(Proprietary::class);
    }
}
