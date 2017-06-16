<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlPoint extends Model
{
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
