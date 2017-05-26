<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DispatchRegister extends Model
{
    protected $table = 'registrodespacho';
    protected $primaryKey = 'id_registro';

    public function getIdAttribute(){
        return $this->attributes['id_registro'];
    }
    public function getVehicleAttribute(){
        return $this->attributes['n_vehiculo'];
    }
    public function getPlateAttribute(){
        return $this->attributes['n_placa'];
    }
    public function getRoundTripAttribute(){
        return $this->attributes['n_vuelta'];
    }
    public function getTurnAttribute(){
        return $this->attributes['n_turno'];
    }
    public function getDispatchTimeAttribute(){
        return $this->attributes['h_reg_despachado'];
    }

    public function reports()
    {
        return $this->hasMany(Report::class,'dispatch_register_id','id_registro');
    }

    public function route()
    {
        return $this->belongsTo(Route::class,'id_ruta','id_rutas');
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
