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
    const ARMENIA = 42;
    const IBAGUE = 43;

    const ALL = [
        self::PCW,
        self::TRANSPUBENZA,
        self::COOTRANSOL,
        self::ALAMEDA,
        self::MONTEBELLO,
        self::URBANUS_MONTEBELLO,
        self::TUPAL,
        self::YUMBENOS,
        self::COODETRANS,
        self::EXPRESO_PALMIRA,
        self::VALLEDUPAR,
        self::ARMENIA,
        self::IBAGUE
    ];

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
    function vehicles()
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
    function activeVehicles()
    {
        return $this->vehicles()->where('active', true)->orderBy('number');
    }

    /**
     * @param null $routeId
     * @return Vehicle|Vehicle[]
     */
    function userVehicles($routeId = null)
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
    function routes()
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
    function activeRoutes()
    {
        return $this->routes()->where('active', true)->orderBy('name');
    }

    /**
     * @param Eloquent $query
     * @return mixed
     */
    function scopeActive($query)
    {
        return $query->where('active', '=', true)->orderBy('short_name');
    }

    /**
     * @param Eloquent $query
     * @return mixed
     */
    function scopeFindAllActive($query)
    {
        return $query->where('active', '=', true)->orderBy('short_name', 'asc')->get();
    }

    /*
     * What companies that have seat sensor counter
     * @return bool
     */
    function isIntermunicipal()
    {
        return collect([self::MONTEBELLO])->contains($this->id);
    }

    /*
     * What companies that have seat sensor counter
     *
     * @return bool
     */
    function hasRecorderCounter()
    {
        return collect([self::ALAMEDA, self::TUPAL, self::PCW, self::TRANSPUBENZA])->contains($this->id);
    }

    /*
     * What companies that have scheduled mileage
     *
     * @return bool
     */
    function hasMileageScheduled()
    {
        return collect([self::ALAMEDA])->contains($this->id);
    }

    /*
     * What companies that have sensor counter
     *
     * @return bool
     */
    function hasSensorCounter()
    {
        return collect([
                self::YUMBENOS,
                self::EXPRESO_PALMIRA,
                self::MONTEBELLO,
                self::TRANSPUBENZA,
                self::VALLEDUPAR,
                Self::ARMENIA
            ])->contains($this->id) || auth()->user()->isAdmin();
    }
    function hasPhoto()
    {
        return collect([
                self::YUMBENOS,
                self::EXPRESO_PALMIRA,
                self::MONTEBELLO,
                self::TRANSPUBENZA,
                self::VALLEDUPAR
            ])->contains($this->id) || auth()->user()->isAdmin();
    }

    /*
     * What companies that have seat sensor recorder counter
     *
     * @return bool
     */
    function hasSensorRecorderCounter()
    {
        return collect([self::TRANSPUBENZA])->contains($this->id);
    }

    function getTypeCounters()
    {
        $counters = collect([]);

        if ($this->hasRecorderCounter()) $counters->push((object)['name' => 'recorders', 'icon' => 'icon-compass']);
        if ($this->hasSensorCounter()) $counters->push((object)['name' => 'sensor', 'icon' => 'fa fa-crosshairs']);
        if ($this->hasSensorRecorderCounter()) $counters->push((object)['name' => 'takings', 'icon' => 'icon-briefcase']);

        return $counters;
    }

    function getSensorRecorderCounterLabel()
    {
        $label = __('Sensor recorder');


        if (collect([
            self::TRANSPUBENZA
        ])->contains($this->id)) {
            $label = __('Taken passengers');
        }

        return $label;
    }

    /*
     * What companies that have Control Point Events Active for send mail report events daily
    */
    function hasControlPointEventsActive()
    {
        return collect([])->contains($this->id);
    }

    /*
     * What companies that have Control Point Events Active for send mail report events daily
    */
    function hasSpeedingEventsActive()
    {
        return $this->id != self::ALAMEDA;
//        return collect([])->contains($this->id);
    }

    /**
     * @return bool
     */
    function hasDriverRegisters()
    {
        return collect([self::ALAMEDA, self::TRANSPUBENZA, self::PCW])->contains($this->id);
    }

    /*
     * What companies that have seat sensor counter
    */
    function hasSeatSensorCounter()
    {
        return collect([self::MONTEBELLO, self::TRANSPUBENZA, self::VALLEDUPAR, self::PCW, self::ARMENIA])->contains($this->id);
    }

    /**
     * @return HasMany | Driver
     */
    function drivers()
    {
        return $this->hasMany(Driver::class)->orderBy('first_name');
    }

    /**
     * @return HasMany
     */
    function activeDrivers()
    {
        return $this->drivers()->active();
    }

    /**
     * @return HasMany
     */
    function dispatches()
    {
        return $this->hasMany(Dispatch::class)->orderBy('name');
    }

    /**
     * @return HasMany
     */
    function proprietaries()
    {
        return $this->hasMany(Proprietary::class);
    }

    /**
     * Checks if company has active the Automatic Dispatch Detection (ADD)
     *
     * @return bool
     */
    function hasADD()
    {
//        return collect([self::MONTEBELLO, self::EXPRESO_PALMIRA, self::VALLEDUPAR])->contains($this->id);
        return collect([self::MONTEBELLO, self::EXPRESO_PALMIRA])->contains($this->id);
    }

    function countMileageByMax()
    {
        return collect([self::ALAMEDA])->contains($this->id);
    }

    /**
     * @return HasMany | User []
     */
    function users()
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
        return collect([self::ALAMEDA, self::TRANSPUBENZA])->contains($this->id);
    }

    function canTakingsByAll()
    {
        return collect([self::TRANSPUBENZA, self::VALLEDUPAR])->contains($this->id);
    }

    function canManualTakings()
    {
        return collect([self::VALLEDUPAR])->contains($this->id);
    }

    function hasTakingsWithMultitariff()
    {
        return collect([self::VALLEDUPAR])->contains($this->id);
    }

    function getTakingsLabel($type)
    {
        $label = 'Other';

        switch ($type) {
            case 'control':
                $label = collect([
                    self::VALLEDUPAR => 'Peaje'
                ])->get($this->id, 'Control');

                break;
            case 'bonus':
                $label = collect([
                    self::VALLEDUPAR => 'Conduce'
                ])->get($this->id, 'Bonus');

                break;
        }

        return __($label);
    }

    function canPhotoRecognition()
    {
        return collect(self::ALL)
            ->forget(self::TRANSPUBENZA)
            ->contains($this->id);
    }
}
