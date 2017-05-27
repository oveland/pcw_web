<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use Mapping;

    protected $table = 'ruta';
    protected $primaryKey = 'id_rutas';

    protected $mapping = [
        'id' => 'id_rutas',
        'distance' => 'distancia',
        'name' => 'nombre'
    ];

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
