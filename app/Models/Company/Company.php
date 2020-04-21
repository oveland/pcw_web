<?php

namespace App\Models\Company;

use App\Models\Drivers\Driver;
use App\Models\Proprietaries\Proprietary;
use App\Models\Routes\Dispatch;
use App\Models\Routes\DispatcherVehicle;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Auth;

/**
 * App\Models\Company\Company
 *
 * @property-read Collection|Vehicle[] $activeVehicles
 * @property-read Collection|Vehicle[] $vehicles
 * @property-read Collection|Driver[] $drivers
 * @property-read Collection|Driver[] $activeDrivers
 * @property-read Collection|Dispatch[] $dispatches
 * @mixin Eloquent
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $nit
 * @property string|null $address
 * @property string $link
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|Route[] $routes
 * @method static Builder|Company whereActive($value)
 * @method static Builder|Company whereAddress($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereLink($value)
 * @method static Builder|Company whereName($value)
 * @method static Builder|Company whereNit($value)
 * @method static Builder|Company whereShortName($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company active()
 * @method static Builder|Company findAllActive()
 * @property string|null $timezone
 * @method static Builder|Company whereTimezone($value)
 * @property-read Collection|Route[] $activeRoutes
 * @property-read Collection|Proprietary[] $proprietaries
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company query()
 * @property int|null $speeding_threshold
 * @property int|null $max_speeding_threshold
 * @method static Builder|Company whereMaxSpeedingThreshold($value)
 * @method static Builder|Company whereSpeedingThreshold($value)
 * @property string|null $default_kmz_url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company\Company whereDefaultKmzUrl($value)
 */
class Company extends Model
{
    const PCW = 6;
    const COOTRANSOL = 12;
    const ALAMEDA = 14;
    const MONTEBELLO = 21;
    const URBANUS_MONTEBELLO = 31;
    const TUPAL = 28;
    const YUMBENOS = 17;
    const COODETRANS = 30;
    const BOOTHS = 34;

    /**
     * @return mixed|string
     */
    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return HasMany
     */
    public function vehicles()
    {
        $user = Auth::user();

        $vehicles = $this->hasMany(Vehicle::class)->orderBy('number');

        if ($user && !$user->isAdmin()) {
            $userVehicleTags = $user->getVehicleTags();

            if ($userVehicleTags->isNotEmpty()) {
                $vehicles->where(function ($query) use ($userVehicleTags) {
                    foreach ($userVehicleTags as $tag) {
                        $query->where('tags', 'like', "%$tag%");
                    }
                });
            }
        }

        return $vehicles;
    }

    /**
     * @return HasMany
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
        if ($this->hasADD() && $routeId && $routeId != 'all') {
            $route = Route::find($routeId);
            if ($route) {
                $vehiclesIdFromDispatcherVehicles = DispatcherVehicle::whereIn('route_id', $route->subRoutes->pluck('id'))->get()->pluck('vehicle_id');
                $vehicles = $vehicles->whereIn('id', $vehiclesIdFromDispatcherVehicles);
            }
        }

        return $vehicles;
    }

    /**
     * @return HasMany
     */
    public function routes()
    {
        $user = Auth::user();
        $routes = $this->hasMany(Route::class)->orderBy('name');

        if($user){
            if ($user->isProprietary()) {
                $assignedVehicles = $user->assignedVehicles(null, true);
                $routes->whereIn('id', DispatcherVehicle::whereIn('vehicle_id', $assignedVehicles->pluck('id'))->pluck('route_id'));
            }else{
                if(!$user->isAdmin()){
                    $userRoutes = $user->getUserRoutes($this);
                    $routes = $routes->whereIn( 'id',  $userRoutes->pluck('id'));
                }
            }
        }

        return $routes;
    }

    /**
     * @return HasMany
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
     * @return bool
     */
    public function isIntermunicipal()
    {
        return collect([self::MONTEBELLO])->contains($this->id);
    }

    /*
     * What companies that have seat sensor counter
     *
     * Alameda
     *
     * @return bool
     */
    public function hasRecorderCounter()
    {
        return collect([self::ALAMEDA, self::TUPAL])->contains($this->id);
    }

    /*
     * What companies that have Control Point Events Active for send mail report events daily
     *
     * Alameda
     *
    */
    public function hasControlPointEventsActive()
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
     * @return HasMany
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class)->orderBy('first_name');
    }

    /**
     * @return HasMany
     */
    public function activeDrivers()
    {
        return $this->drivers()->active();
    }

    /**
     * @return HasMany
     */
    public function dispatches()
    {
        return $this->hasMany(Dispatch::class)->orderBy('name');
    }

    /**
     * @return HasMany
     */
    public function proprietaries()
    {
        return $this->hasMany(Proprietary::class);
    }

    /**
     * Checks if company has active the Automatic Dispatch Detection (ADD)
     *
     * @return bool
     */
    public function hasADD()
    {
        return collect([self::MONTEBELLO])->contains($this->id);
    }
}
