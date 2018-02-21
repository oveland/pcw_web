<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * App\Vehicle
 *
 * @property int $id
 * @property string $plate
 * @property string $number
 * @property int $company_id
 * @property bool $active
 * @property bool $in_repair
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereInRepair($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vehicle whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\SimGPS $simGPS
 */
class Vehicle extends Model
{
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query){
        return $query->where('active','=',true);
    }

    public function simGPS()
    {
        return $this->hasOne(SimGPS::class);
    }
}
