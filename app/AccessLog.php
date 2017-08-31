<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $table = 'acceso_historial';

    public function user()
    {
        return $this->belongsTo(UserLog::class,'user_id','id_usuario');
    }
}
