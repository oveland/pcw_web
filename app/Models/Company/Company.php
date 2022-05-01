<?php

namespace App\Models\Company;

use App\Models\LM\Config;
use App\Models\Drivers\Driver;
use App\Models\Proprietaries\Proprietary;
use App\Models\Routes\Dispatch;
use App\Models\Routes\DispatcherVehicle;
use App\Models\Routes\Route;
use App\Models\Users\User;
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
 * @method static Builder|Company whereDefaultKmzUrl($value)
 * @property-read Collection|User[] $users
 */
class Company extends Model
{
    const PCW = 6;
    const TRANSPUBENZA = 2;
    const COOTRANSOL = 12;
    const ALAMEDA = 14;
    const MONTEBELLO = 21;
    const URBANUS_MONTEBELLO = 31;
    const TUPAL = 28;
    const YUMBENOS = 17;
    const COODETRANS = 30;
    const BOOTHS = 34;
    const SOTRAVALLE = 35;
    const EXPRESO_PALMIRA = 39;
    const VALLEDUPAR = 41;

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

        if ($user) {
            if (!$user->isAdmin()) {
                $userVehicleTags = $user->getVehicleTags();

                if ($userVehicleTags->isNotEmpty()) {
                    $vehicles->where(function ($query) use ($userVehicleTags) {
                        foreach ($userVehicleTags as $tag) {
                            $query->where('tags', 'like', "%$tag%");
                        }
                    });
                }
            } else if (!$user->isSuperAdmin()) {
                $vehicles->where('id', '<>', 1873); // Exclude vh 02 copied from company PARTICULARES. TODO: Delete when tests for GPS Syrus ends
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
        $vehicles = $user ? $user->assignedVehicles($this) : $this->activeVehicles;
        if ($this->hasADD() && $routeId && $routeId != 'all') {
            $route = Route::find($routeId);
            if ($route) {
                $vehiclesIdFromDispatcherVehicles = DispatcherVehicle::whereIn('route_id', $route->subRoutes->pluck('id'))->get()->pluck('vehicle_id');
                if ($vehiclesIdFromDispatcherVehicles->count()) {
                    $vehicles = $vehicles->whereIn('id', $vehiclesIdFromDispatcherVehicles);
                }
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

        if ($user && !$user->isAdmin()) {
            $userRoutes = $user->getUserRoutes($this);
            $routes = $routes->whereIn('id', $userRoutes->pluck('id'));
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
     * @param Eloquent $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('active', '=', true)->orderBy('short_name');
    }

    /**
     * @param Eloquent $query
     * @return mixed
     */
    public function scopeFindAllActive($query)
    {
        return $query->where('active', '=', true)->orderBy('short_name', 'asc')->get();
    }

    /*
     * What companies that have seat sensor counter
     * @return bool
     */
    public function isIntermunicipal()
    {
        return collect([self::MONTEBELLO])->contains($this->id);
    }

    /*
     * What companies that have seat sensor counter
     *
     * @return bool
     */
    public function hasRecorderCounter()
    {
        return collect([self::ALAMEDA, self::TUPAL, self::PCW, self::TRANSPUBENZA])->contains($this->id);
    }

    /*
     * What companies that have sensor counter
     *
     * @return bool
     */
    public function hasSensorCounter()
    {
        return collect([
                self::YUMBENOS,
                self::ALAMEDA,
                self::EXPRESO_PALMIRA,
                self::MONTEBELLO,
                self::TRANSPUBENZA
            ])->contains($this->id) || auth()->user()->isAdmin();
    }

    /*
     * What companies that have Control Point Events Active for send mail report events daily
    */
    public function hasControlPointEventsActive()
    {
        return collect([])->contains($this->id);
    }

    /*
     * What companies that have Control Point Events Active for send mail report events daily
    */
    public function hasSpeedingEventsActive()
    {
        return $this->id != self::ALAMEDA;
//        return collect([])->contains($this->id);
    }

    /**
     * @return bool
     */
    public function hasDriverRegisters()
    {
        return collect([self::ALAMEDA, self::TRANSPUBENZA, self::PCW])->contains($this->id);
    }

    /*
     * What companies that have seat sensor counter
    */
    public function hasSeatSensorCounter()
    {
        return collect([self::MONTEBELLO, self::TRANSPUBENZA, self::VALLEDUPAR, self::PCW])->contains($this->id);
    }

    /**
     * @return HasMany | Driver
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
//        return collect([self::MONTEBELLO, self::EXPRESO_PALMIRA, self::VALLEDUPAR])->contains($this->id);
        return collect([self::MONTEBELLO, self::EXPRESO_PALMIRA])->contains($this->id);
    }

    public function countMileageByMax()
    {
        return collect([self::ALAMEDA])->contains($this->id);
    }

    /**
     * @return HasMany | User []
     */
    public function users()
    {
        return $this->hasMany(User::class)->where('active', true);
    }

    function getMaxDailyMileage()
    {
        switch ($this->id) {
            case self::MONTEBELLO:
                return 900000;
                break;
            case self::YUMBENOS:
                return 500000;
                break;
            default:
                return 400000;
                break;
        }
    }

    /**
     * @param $key
     * @param bool $returnValue
     * @return mixed
     */
    public function configBEA($key, $returnValue = false)
    {
        $config = $this->hasMany(Config::class)->where('key', $key)->first();

        if (!$config) return false;

        return $returnValue ? $config->value : $config->active;
    }

    public function getAPIFields()
    {
        return (object)$this->only(['id', 'name', 'short_name', 'active']);
    }

    public function hasRouteTakings()
    {
        return collect([self::ALAMEDA])->contains($this->id);
    }
}
