<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function controlPoints(){
        return $this->hasMany(ControlPoint::class)->orderBy('order','asc');
    }
}
