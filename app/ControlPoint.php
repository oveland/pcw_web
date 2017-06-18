<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlPoint extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
