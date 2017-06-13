<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

class Vehicle extends Model
{
    public function company(){
        return $this->belongsTo(Company::class);
    }
}
