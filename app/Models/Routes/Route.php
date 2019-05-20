<?php

namespace App\Models\Routes;

use App\Models\Company\Company;
use App\Models\Vehicles\CurrentLocation;
use Illuminate\Database\Eloquent\Model;

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
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Company\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\ControlPoint[] $controlPoints
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereRoadTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereUrl($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route active()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\Fringe[] $fringes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Routes\CurrentDispatchRegister[] $currentDispatchRegisters
 * @property-read \App\Models\Routes\Dispatch $dispatch
 * @property bool|null $as_group
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route whereAsGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Routes\Route query()
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
        return (object) $dataAPI;
    }
}
