<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Route
 *
 * @property-read \App\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ControlPoint[] $controlPoints
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property int $distance
 * @property int $road_time
 * @property int $company_id
 * @property int $dispatch_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $url
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
 */
class Route extends Model
{
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function controlPoints(){
        return $this->hasMany(ControlPoint::class)->orderBy('order','asc');
    }
}
