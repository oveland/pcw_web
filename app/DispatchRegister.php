<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DispatchRegister extends Model
{
    use Mapping;

    protected $table = 'registrodespacho';
    protected $primaryKey = 'id_registro';

    protected $mapping = [
        'id' => 'id_registro',
        'vehicle' => 'n_vehiculo',
        'plate' => 'n_placa',
        'round_trip' => 'n_vuelta',
        'turn' => 'n_turno',
        'dispatch_time' => 'h_reg_despachado'
    ];

    public function getDispatchTimeAttribute(){
        return $this->attributes['h_reg_despachado'];
    }

    public function reports()
    {
        return $this->hasMany(Report::class,'dispatch_register_id','id_registro')->orderBy('date','asc');
    }

    public function route()
    {
        return $this->belongsTo(Route::class,'id_ruta','id');
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
