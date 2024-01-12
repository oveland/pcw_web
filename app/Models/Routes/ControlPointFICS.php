<?php

namespace App\Models\Routes;

use Illuminate\Database\Eloquent\Model;

class ControlPointFICS extends Model
{
    protected $table = 'control_points_fics';

    function controlPoint() {
        return $this->belongsTo(ControlPoint::class);
    }
}
