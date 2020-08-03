<?php

namespace App\Models\Routes;

use App\Models\Company\Company;
use App\Models\Vehicles\CurrentLocation;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Routes\Route
 *
 * @property int $id
 * @property string $name
 * @property int $distance
 * @property int $road_time
 * @property string $url
 * @property int $company_id
 * @property int $dispatch_id
 * @property int $bea_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Company\Company $company
 * @property-read Collection|ControlPoint[] $controlPoints
 * @method static Builder|Route whereActive($value)
 * @method static Builder|Route whereCompanyId($value)
 * @method static Builder|Route whereCreatedAt($value)
 * @method static Builder|Route whereDispatchId($value)
 * @method static Builder|Route whereDistance($value)
 * @method static Builder|Route whereId($value)
 * @method static Builder|Route whereName($value)
 * @method static Builder|Route whereRoadTime($value)
 * @method static Builder|Route whereUpdatedAt($value)
 * @method static Builder|Route whereUrl($value)
 * @mixin Eloquent
 * @method static Builder|Route active()
 * @property-read Collection|\App\Models\Routes\Fringe[] $fringes
 * @property-read Collection|CurrentDispatchRegister[] $currentDispatchRegisters
 * @property-read Dispatch $dispatch
 * @property bool|null $as_group
 * @method static Builder|Route whereAsGroup($value)
 * @method static Builder|Route newModelQuery()
 * @method static Builder|Route newQuery()
 * @method static Builder|Route query()
 * @property string|null $min_route_time
 * @method static Builder|Route whereBeaId($value)
 * @method static Builder|Route whereMinRouteTime($value)
 * @property int|null $route_id
 * @property-read Collection|Route[] $subRoutes
 * @method static Builder|Route whereRouteId($value)
 * @property-read RouteGoogle $routeGoogle
 * @property int $distance_threshold
 * @property int $sampling_radius
 * @method static Builder|Route whereDistanceThreshold($value)
 * @method static Builder|Route whereSamplingRadius($value)
 * @property-read RouteTariff $tariff
 */
class Route extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function controlPoints()
    {
        return $this->hasMany(ControlPoint::class)->orderBy('order', 'asc');
    }

    public function belongsToCompany($company)
    {
        return $this->company->id == $company->id;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function fringes($dayType = 1)
    {
        return $this->hasMany(Fringe::class)->where('day_type_id', $dayType)->get();
    }

    public function currentLocations()
    {
        $currentRouteDispatchRegisters = $this->currentDispatchRegisters;
        return CurrentLocation::whereIn('dispatch_register_id', $currentRouteDispatchRegisters->pluck('dispatch_register_id'))->get();
    }

    public function currentDispatchRegisters()
    {
        return $this->hasMany(CurrentDispatchRegister::class);
    }

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function getAPIFields()
    {
        $dataAPI = $this->toArray();
        $dataAPI['company'] = $this->company->toArray();
        return (object)$dataAPI;
    }

    public function routeGoogle()
    {
        return $this->hasOne(RouteGoogle::class, 'id_ruta', 'id');
    }

    /**
     * @return Route[]|HasMany
     */
    function subRoutes()
    {
        if ($this->as_group) {
            return $this->hasMany(Route::class)->active();
        } else {
            return $this->hasMany(Route::class)->where('id', $this->id);
        }
    }

    /**
     * @return RouteTariff
     */
    function getTariffAttribute()
    {
        $tariff = RouteTariff::where('route_id', $this->id)->first();
        if (!$tariff) {
            $tariff = new RouteTariff();
            $tariff->route()->associate($this);
            $tariff->value = 0;
        }
        return $tariff;
    }
}
