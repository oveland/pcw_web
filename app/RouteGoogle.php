<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteGoogle extends Model
{
    use Mapping;

    protected $table = 'rutas_google_v3';
    protected $primaryKey = 'id_ruta';

    protected $mapping = [
        'id' => 'id_ruta',
        'url' => 'url'
    ];
}
