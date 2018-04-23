<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Route
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
 * @property-read \App\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ControlPoint[] $controlPoints
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereDispatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereRoadTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route whereUrl($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Route active()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Fringe[] $fringes
 */
class Route extends Model
{
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function controlPoints(){
        return $this->hasMany(ControlPoint::class)->orderBy('order','asc');
    }

    public function belongsToCompany($company){
        return $this->company->id == $company->id;
    }

    public function scopeActive($query){
        return $query->where('active',true);
    }

    public function fringes($dayType)
    {
        return $this->hasMany(Fringe::class)->where('day_type_id',$dayType)->get();
    }
}
