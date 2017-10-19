<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RouteGoogle
 *
 * @property int $id_ruta
 * @property string|null $url
 * @property string|null $coordenadas
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RouteGoogle whereCoordenadas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RouteGoogle whereIdRuta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RouteGoogle whereUrl($value)
 * @mixin \Eloquent
 */
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
