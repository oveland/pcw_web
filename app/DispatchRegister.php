<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DispatchRegister extends Model
{
    protected $table = 'registrodespacho';

    public function reports()
    {
        return $this->hasMany(Report::class,'dispatch_register_id','id_registro')->orderBy('date','asc');
    }

    public function route()
    {
        return $this->belongsTo(Route::class,'id_ruta','id_rutas');
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
