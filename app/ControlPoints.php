<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlPoints extends Model
{
    use Mapping;
    protected $table = 'puntos_control_ruta';
    protected $primaryKey = 'secpuntos_control_ruta';

    protected $mapping = [
        'id' => 'secpuntos_control_ruta',
        'latitude' => 'lat',
        'longitude' => 'lng',
        'name' => 'nombre',
        'route_id' => 'id_ruta',
        'course' => 'trayecto',
        'order' => 'orden',
        'type' => 'tipo',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class, 'id_ruta', 'id');
    }
}
